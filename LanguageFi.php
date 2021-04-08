<?php
/**
 * Finnish (Suomi) specific code.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @file
 * @author Niklas Laxström
 * @ingroup Language
 */

/**
 * Finnish (Suomi)
 *
 * @ingroup Language
 */
class LanguageFi extends Language {
  /**
   * Convert from the nominative form of a noun to some other case
   * Invoked with {{grammar:case|word}}
   *
   * @param string $word
   * @param string $case
   * @return string
   */
  public function convertGrammar( $word, $case, $test = null ) {
    global $wgGrammarForms;
    if ( isset( $wgGrammarForms['fi'][$case][$word] ) ) {
      return $wgGrammarForms['fi'][$case][$word];
    }
 
    # wovel harmony flag
    $aou = preg_match( '/[aou][^äöy]*$/i', $word );

    $pattern = '/('.
      '((ar|p|(?<=a)t|n|(?<=r)r|(?<=l)l|(?<=ar)v)?(?<!jal|ann|erv)a|'.
          '(p|(?<!s)t|n|r|(?<=l)l|(?<=y)v)?ä|'.
          '((?<=r)v|(?<=m)i)?(?<![il]v)e|(?<=ru)i|'.
          '(?<=arta|e|i|u)u|(?<=e|i|y)y)?s'.
      '|(?<=n)nel|mel|val|(?<!ve)l'.		   # L
      '|(ne|di|(?<=a|är)vi|(?<=t)i|o|ö|(?<!e))n'.  # N
      '|(n[ae]|(?<=t)[aä])r'.		'|(my)?t'. # R and T
      '|(?<=po|\ba)ika|(?<=y)lkä|(?<!i)uku|yky'.
      '|([tnr]t|[mp]?p|nk)[äoöuy]|([tnr]t|[mp]?p|(?<!i)nk)a'.
      '|(?<!h)k[äoöuy]|(?<=(?<!h|in)|nah|\buh)ka'.
      '|(?<![ae]h)ko|(?<!s|au)l?to|(?<!s)l?t[aäöuy]'.
      '|(?<!a)a|(?<!ä)ä|i[oö]|(?<!u|o)o|(?<!y|ö)ö|(?<!i|u)u|(?<!y)y'.
      '|((?<=l)l|(?<=n)n|[lh]j|(?<!t)i|[aädp]|(?<!s)[tk]|'.
          '(?<!oi|er|y)v|(?<!i|e))e'.
      '|t?ti'.
      '|(a|ä|\bo|(?<!r)e)?nki'.
      '|(r|l|k|(?<=l)a)?(?<!wi)ki'.
      '|((?<=\b|h)a|(?<=t)y)?ppi|pi|(?<!h)vi'.
      '|((?<=\bla)p|r)si'.
      '|((?<!a|[^e]i)t|(?<![^u]o|e)k)si'.# -eitsi,vuoksi
      '|(?<=paa|vuo|su|[mv]e|kä|[hv]ii)si'. # paasi,vuosi,susi,vesi,käsi,hiisi
      '|((?<=l)[ae]|(?<=t)ä)?hti|mpi'.
      '|((?<=\bn)i|(?<!a|n[io]|puo))mi'. # (mi)nimi,-nomi,puomi,raami
      '|(?<=s|m|v|k|(?<!e)n|(?<!e|o|\btu|\btii)l|'.
          '(?<!o|\b[mv]e|\b[sj]uu|kaa)r)(?<!(jo|ku)us|\bään)i'.
    ')$/u';
    $ar = preg_split ( $pattern , $word , -1 , PREG_SPLIT_DELIM_CAPTURE); #|PREG_SPLIT_NO_EMPTY );
    if ($test && $ar[0] != $test)
      return $ar[0].'_'.(count($ar) > 1 ? $ar[1] : '');
 
    if (count($ar) > 1 )
      $ar = array_slice($ar, 0, 2); # discard other possible capturing groups
    else {
      $ar[1] = '';
      if ( preg_match( '/i$/', $ar[0] ) ) {
        $ar[0] = substr($ar[0], 0, -1);
        if (preg_match('/\b[vm]er$/', $ar[0]) && $case == 'partitive')
          $aou = true; # the wovel harmony of 'veri' & 'meri' is complicated
      }
    }
    
    $ar[1] = preg_replace(
      [ '/(?<=u|y)s$/',	'/(?<=.(nk|pp|ht)|im|rk|[ktp]s|mp)i$/',
        '/^si$/', '/(?<=^[pvk]|lk)i$/' ],				# > e->€
      [ 'te', 'e',
        't€', '€' ], $ar[1]);

    switch ( $case ) {
      case 'genitive': case 'inessive': case 'elative':
        $k = preg_match('/([aiu])([aou])k\2$/', $word) ? '\'' : '';
        $ar[1] = preg_replace(
          [ '/(?<=u|^y)t(?=e$)'.	'|^t(?=([aouy]|ä|ö)$)/',	# > TTA
            '/(?<=^[uy])k(?=[uy]$)'.	'|^p(?=([aouy]|ä)$)/',		# > PPA
            '/^ik(?=a$)/', '/(?<=n)k/', '/^(t|p)(?=\1([aouy]|ä|ö)$)/',	# < ===
            '/^([nrl](?=t)|m(?=p))./',
            '/(?<=h)t/', '/(?<=r|l)k/', '/(?<=^r)si$/', 
            '/^k(?=([aouy]|ä|ö)$)/', '/(?<=^[ka])k(?=i$)/',
            '/^t(?=i$)/', '/^t(?=ti$)/',			    # TI  > TTI
            '/pp/', '/^p(?=€$)/', '/^t(?=€$)/', '/^k(?=€$)/' ],	    # >(SI)VE E
          [ 'd',
            'v',
            'j', 'g', '',
            '\1\1',
            'd', 'j', 're',
            $k, '',
            'd', '',
            'p', 'v', 'd', '' ], $ar[1]);
      case 'illative':
        $e = preg_match( '/([aeo]|(?<=i|u)u|(?<=y)y|ä|ö)$/', $ar[0] ) ? '':'e';
        $ar[1] = preg_replace(
          [ '/^(?=e$)/', '/^$/', '/^([kai]|ä)e$/',		    # >(KI)   T
            '/^[lnd](?=e$)/', '/^(te)$/',			    # >(SI)   L
            '/^v(?=e$)/', '/^(pe)$/',				    # > _€
            '/(?<=^[lh])j(?=e)/', '/(?<=^r)si$/',
            '/(?<=^ar)(?=as$)/', '/^(p|t)(?=(a|ä)s$)/', '/^[nrl](?=(a|ä)s$)/',
            '/^v(?=(a|ä)s$)/',
            '/(?<=^ie)s$/', '/^(?=[ei]s$)/', '/(?<=u|^y)t$/',
            '/([aei]|ä)s$/', '/s$/',
            '/nen$/', '/vin$/', '/d?in$/', '/on$/', '/ön$/', '/(n|mi)$/',
            '/n?(a|ä)r$/', '/ne(r|l)$/', '/.(e|a)l$/', '/l$/',		# <---L 
            '/^m(?=yt)/', '/t$|€$/' ],				    # < €->e -T
          [ 'e', $e, '\1kee',
            'te', 't\1e',
            'pe', 'p\1e',
            'ke', 'te',
            'k', '\1\1', 't',
            'p',
            'he', 'k', 'd',
            '\1\1', 'kse',
            'se', 'pime', 'time', 'toma', 'tömä', 'me',
            't\1re', 'te\1e', 'p\1le', 'le', 
            'p', 'e' ], $ar[1]);
        break;
      case 'partitive':
        $ar[1] = preg_replace(
          [ '/nen$/', '/(?<=^|[strln]|io|iö)$|(?<=^[uy]t)e$/', 
            '/mi$/', '/^[tp]se$/',
            '/(?<=^r)si$|(?<=(?<![nlr]k|[mp]p|ht|im|ks)e)$|^t€$/', '/€$/' ],
          [ 's', 't', 
            'nt', 'st',
            'tt', 'e' ], $ar[1]);
        break;
    }
    $word = implode('', $ar); 

    # The flag should be false for compounds where the last word has only neutral vowels (e/i).
    # The general case cannot be handled without a dictionary, but there's at least one notable
    # special case we should check for:

    if ( preg_match( '/wiki$/i', $word ) ) {
      $aou = false;
    }

    switch ( $case ) {
      case 'genitive':
        $word .= 'n';
        break;
      case 'partitive':
        $word .= ( $aou ? 'a' : 'ä' );
        break;
      case 'inessive':
        $word .= ( $aou ? 'ssa' : 'ssä' );
        break;
      case 'elative':
        $word .= ( $aou ? 'sta' : 'stä' );
        break;
      case 'illative':
        # Double the last letter and add 'n'
        #echo $word.':';
        $word = preg_replace(
          [ '/((?<!m)a|((?<!\bt)|(?<=ät))e|i|o|(?<=[st]t|(?<!\b)k)u'.
                     '|(?<=t|v|(?<!\b)p)ä)\1$/',
            '/(ä|ö|[^n])$/', # unless last letter is 'n', double it and  add 'n'
            '/((?<=ma)a|(?<=i|e)e|(?<=i|u)u|(?<=u)o'.
                      '|(?<=y)y|(?<=[yö])ö|(?<=ä)ä)\1n$/' ],
          [ '\1\1se',
            '\1\1n',
            '\1h\1n' ], $word);
        break;
    }
    return $word;
  }

	/**
	 * @param string $str
	 * @param User|null $user User object to use timezone from or null for $wgUser
	 * @param int $now Current timestamp, for formatting relative block durations
	 * @return string
	 */
	public function translateBlockExpiry( $str, User $user = null, $now = 0 ) {
		/*
			'ago', 'now', 'today', 'this', 'next',
			'first', 'third', 'fourth', 'fifth', 'sixth', 'seventh', 'eighth', 'ninth',
				'tenth', 'eleventh', 'twelfth',
			'tomorrow', 'yesterday'

			$months = 'january:tammikuu,february:helmikuu,march:maaliskuu,april:huhtikuu,' .
				'may:toukokuu,june:kesäkuu,july:heinäkuu,august:elokuu,september:syyskuu,' .
				'october:lokakuu,november:marraskuu,december:joulukuu,' .
				'jan:tammikuu,feb:helmikuu,mar:maaliskuu,apr:huhtikuu,jun:kesäkuu,' .
				'jul:heinäkuu,aug:elokuu,sep:syyskuu,oct:lokakuu,nov:marraskuu,' .
				dec:joulukuu,sept:syyskuu';
		*/
		$weekds = [
			'monday' => 'maanantai',
			'tuesday' => 'tiistai',
			'wednesday' => 'keskiviikko',
			'thursday' => 'torstai',
			'friday' => 'perjantai',
			'saturday' => 'lauantai',
			'sunday' => 'sunnuntai',
			'mon' => 'ma',
			'tue' => 'ti',
			'tues' => 'ti',
			'wed' => 'ke',
			'wednes' => 'ke',
			'thu' => 'to',
			'thur' => 'to',
			'thurs' => 'to',
			'fri' => 'pe',
			'sat' => 'la',
			'sun' => 'su',
			'next' => 'seuraava',
			'tomorrow' => 'huomenna',
			'ago' => 'sitten',
			'seconds' => 'sekuntia',
			'second' => 'sekunti',
			'secs' => 's',
			'sec' => 's',
			'minutes' => 'minuuttia',
			'minute' => 'minuutti',
			'mins' => 'min',
			'min' => 'min',
			'days' => 'päivää',
			'day' => 'päivä',
			'hours' => 'tuntia',
			'hour' => 'tunti',
			'weeks' => 'viikkoa',
			'week' => 'viikko',
			'fortnights' => 'tuplaviikkoa',
			'fortnight' => 'tuplaviikko',
			'months' => 'kuukautta',
			'month' => 'kuukausi',
			'years' => 'vuotta',
			'year' => 'vuosi',
			'infinite' => 'ikuinen',
			'indefinite' => 'ikuinen',
			'infinity' => 'ikuinen'
		];

		$final = '';
		$tokens = explode( ' ', $str );
		foreach ( $tokens as $item ) {
			if ( !is_numeric( $item ) ) {
				if ( count( explode( '-', $item ) ) == 3 && strlen( $item ) == 10 ) {
					list( $yyyy, $mm, $dd ) = explode( '-', $item );
					$final .= ' ' . $this->date( "{$yyyy}{$mm}{$dd}000000" );
					continue;
				}
				if ( isset( $weekds[$item] ) ) {
					$final .= ' ' . $weekds[$item];
					continue;
				}
			}

			$final .= ' ' . $item;
		}

		return trim( $final );
	}
}
