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

\score {
  <<
    \new ChoirStaff <<
      \new Staff \with {
		$minifySoprano \magnifyStaff #5/7        midiInstrument = "choir aahs"
        instrumentName = "Soprano"
      } { \soprano \bar "|." }
      $verseLinks
      \new Staff \with {
        midiInstrument = "choir aahs"
        instrumentName = "Alto"
      } { \alto \bar "|."  }
      $verseLinks
      \new Staff \with {
        midiInstrument = "choir aahs"
        instrumentName = "Tenor"
      } { \clef "treble_8" \tenor  \bar "|." }
      $verseLinks
      \new Staff \with {
        midiInstrument = "choir aahs"
        instrumentName = "Bass"
      } { \clef bass \bass  \bar "|." }
    >>
   $pianoReduction \pianoReduction
  >>
  \layout { }
  \midi {
    \tempo 4=100
  }
}