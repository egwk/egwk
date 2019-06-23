\version "2.18.2"

#(define ((time-double-time up down upp downp) grob)
   (grob-interpret-markup grob
     (markup #:override '(baseline-skip . 0) #:number
       (#:line (
           (#:column (up down))
           (#:column (upp downp))
            )))))

#(define ((time-parenthesized-time up down upp downp) grob)
   (grob-interpret-markup grob
     (markup #:override '(baseline-skip . 0) #:number
       (#:line (
           (#:column (up down))
           #:vcenter "("
           (#:column (upp downp))
           #:vcenter ")" )))))


\header {
	$header	title = "$title"
	$header	poet = "$poet"
	$header composer = "$composer"
	$header arranger = "$arranger"
			tagline = "$tagline"
}

global = {
  \key $key
  $time
  $partial
}

soprano = \relative c' {
  \global
  $soprano
}

alto = \relative c' {
  \global
  $alto
}

tenor = \relative c {
  \global
  $tenor
}

bass = \relative c {
  \global
  $bass
}

$verses

pianoReduction = \new PianoStaff \with {
  fontSize = #-1
  \override StaffSymbol #'staff-space = #(magstep -1)
} <<
  \new Staff \with {
    \consists "Mark_engraver"
    \consists "Metronome_mark_engraver"
    \remove "Staff_performer"
  } {
    #(set-accidental-style 'piano)
    <<
      \soprano \\
      \alto
    >>
  }
  \new Staff \with {
    \remove "Staff_performer"
  } {
    \clef bass
    #(set-accidental-style 'piano)
    <<
      \tenor \\
      \bass
    >>
  }
>>

\paper {
  left-margin = 13
  $mobileSize #(set! paper-alist (cons '("mobile size" . (cons (* 4 in) (* 30 in))) paper-alist))
  $mobileSize #(set-paper-size "mobile size")
  $tabletSize #(set! paper-alist (cons '("tablet size" . (cons (* 5.5 in) (* 30 in))) paper-alist))
  $tabletSize #(set-paper-size "tablet size")
}

\score {

  <<
    \new ChoirStaff <<
      \new Staff = "sa" \with {
		\magnifyStaff #(magstep -3)
		$minifySoprano \magnifyStaff #4/7
        midiInstrument = "choir aahs"
        instrumentName = \markup \center-column { "Soprano" }
      } <<
        \new Voice = "soprano"{
        \override BreathingSign.Y-offset = #0.4
          \override BreathingSign.text =
            \markup { "|" }

        \voiceOne \soprano \bar "|." }
      >>
      
      $verseLinks
      
      \new Staff = "tb" \with {
        midiInstrument = "choir aahs"
        instrumentName = \markup \center-column { "Tenor" "Bass" }
      } <<
        \clef bass
        \new Voice = "tenor" { \voiceOne \tenor \bar "|." }
        \new Voice = "bass" { \voiceTwo \bass \bar "|." }
      >>
    >>
    $pianoReduction \pianoReduction
  >>
  \layout { }
  \midi {
    \tempo 4=80
  }
}
