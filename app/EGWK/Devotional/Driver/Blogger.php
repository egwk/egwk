<?php
/**
 * Created by PhpStorm.
 * User: peter_erdodi
 * Date: 17/03/2019
 * Time: 17:38
 */

namespace App\EGWK\Devotional\Driver;


use App\EGWK\Devotional\Driver;

abstract class Blogger implements Driver
{

    /**
     * @var Google_Service_Blogger
     */
    protected $service;
    protected $id;
    protected $timezone;
    protected $appname;
    protected $keyfile;
    protected $url;
    protected $scopes;

    /**
     * Blogger constructor.
     * @param string $id
     * @param string $appName
     * @throws \Google_Exception
     */
    public function __construct(string $id)
    {
        $this->id = $id;

        $this->timezone = config("EGWK.devotional.$id.timezone");
        $this->appname = config("EGWK.devotional.$id.appname");
        $this->keyfile = config("EGWK.devotional.$id.keyfile");
        $this->url = config("EGWK.devotional.$id.url");
        $this->scopes = config("EGWK.devotional.$id.scopes");

        $this->service = $this->getService();
    }

    /**
     * @return \Google_Service_Blogger
     * @throws \Google_Exception
     */
    protected function getService()
    {
        $client = new \Google_Client();
        $client->setApplicationName($this->appname);
        $client->setAuthConfig($this->keyfile);
        $client->setScopes($this->scopes);
        $service = new \Google_Service_Blogger($client);

        return $service;
    }

    /**
     * @param string $str
     * @return string
     */
    protected function filterText(string $str): string
    {
        return html_entity_decode(
            implode("\n",
                array_filter(
                    array_map("trim",
                        explode("\n",
                            strip_tags($str)
                        )
                    )
                )
            )
        );
    }

    /**
     * @param array $optParams
     * @return array
     */
    protected function common(array $optParams = []): array
    {
        $blog = $this->service->blogs->getByUrl($this->url);
        $blogId = $blog->getId();
        $posts = [];
        foreach (
            $this->service->posts->listPosts($blogId, $optParams)
            as $post) {
            $postObj = $post->toSimpleObject();
            $postObj->text = $this->filterText($postObj->content);
            $postObj->author =  $postObj->author->displayName;
            $posts[] = collect($postObj)->only(['author', 'content', 'text', 'labels', 'published', 'title', 'url']);
        }
        return $posts;
    }

    /**
     * @inheritdoc
     */
    public function all(): array
    {
        return $this->common(['maxResults' => 500]);
    }

    /**
     * @inheritdoc
     */
    public function year(string $year = null): array
    {
        $year = $year ?: date('Y');
        return $this->common([
            'maxResults' => 370,
            'startDate' => $year . '-01-01T00:00:00' . $this->timezone,
            'endDate' => $year . '-12-31T23:59:59' . $this->timezone,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function today(): array
    {
        return $this->common([
            'maxResults' => 1,
            'startDate' => date('Y-m-d') . 'T00:00:00' . $this->timezone]);
    }

    /**
     * @inheritdoc
     */
    public function date(string $date): array
    {
        $date = date('Y-m-d', strtotime($date)) . 'T23:59:59' . $this->timezone;
        return $this->common([
            'maxResults' => 1,
            'endDate' => $date]);
    }

}
