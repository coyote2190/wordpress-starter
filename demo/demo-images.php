<?php
/**
 * Fausses images de la démo : petites illustrations dessinées avec GD, aux
 * couleurs de la charte (lavande, encre, aubergine, rose pâle).
 *
 * Chargé par demo/demo-content.php (WP-CLI uniquement). Aucune image n'est
 * stockée dans le thème : tout est généré, puis ajouté à la médiathèque.
 *
 * Scènes disponibles (paramètre $scene de starter_demo_image()) :
 * - site     : fenêtre de navigateur avec une page d'accueil (titre, bouton, visuel) ;
 * - shop     : fenêtre de navigateur avec une grille de produits ;
 * - brand    : identité visuelle (logo, nuancier, « Aa ») ;
 * - abstract : composition de formes (soleil, arche, carrés) ;
 * - avatar   : portrait stylisé (variante = n° de 0 à 3) ;
 * - studio   : bureau du studio (ordinateur, plante, mug, moodboard).
 *
 * Le dessin se fait au double de la taille puis est réduit : les bords sont lissés.
 *
 * @package Starter
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	exit;
}

/** Facteur de suréchantillonnage (anticrénelage). */
const STARTER_DEMO_SCALE = 2;

/**
 * Couleurs de la charte utilisées par les illustrations.
 *
 * @return array<string, string>
 */
function starter_demo_colors(): array {
	return array(
		'ink'      => '#1C1C1C',
		'plum'     => '#3E3852',
		'lavender' => '#D6CCF5',
		'lilac'    => '#B9AEE6',
		'mist'     => '#EEEAFB',
		'pink'     => '#F7E3F4',
		'paper'    => '#FAF9F7',
		'line'     => '#E4E1DC',
		'grey'     => '#D9D6E0',
		'white'    => '#FFFFFF',
	);
}

/**
 * Alloue une couleur (#rrggbb), avec transparence facultative (0 = opaque, 127 = invisible).
 *
 * @param \GdImage $img Image.
 */
function starter_demo_rgb( $img, string $hex, int $alpha = 0 ): int {
	[ $r, $g, $b ] = sscanf( ltrim( $hex, '#' ), '%02x%02x%02x' );
	return imagecolorallocatealpha( $img, $r, $g, $b, $alpha );
}

/**
 * Rectangle aux coins arrondis, plein.
 *
 * @param \GdImage $img Image.
 */
function starter_demo_rrect( $img, float $x1, float $y1, float $x2, float $y2, float $r, int $color ): void {
	$r = max( 0, min( $r, ( $x2 - $x1 ) / 2, ( $y2 - $y1 ) / 2 ) );
	[ $x1, $y1, $x2, $y2, $r ] = array_map( 'intval', array( $x1, $y1, $x2, $y2, $r ) );

	if ( $r < 1 ) {
		imagefilledrectangle( $img, $x1, $y1, $x2, $y2, $color );
		return;
	}
	imagefilledrectangle( $img, $x1 + $r, $y1, $x2 - $r, $y2, $color );
	imagefilledrectangle( $img, $x1, $y1 + $r, $x1 + $r - 1, $y2 - $r, $color );
	imagefilledrectangle( $img, $x2 - $r + 1, $y1 + $r, $x2, $y2 - $r, $color );
	foreach ( array( array( $x1 + $r, $y1 + $r, 180, 270 ), array( $x2 - $r, $y1 + $r, 270, 360 ), array( $x1 + $r, $y2 - $r, 90, 180 ), array( $x2 - $r, $y2 - $r, 0, 90 ) ) as [ $cx, $cy, $start, $end ] ) {
		imagefilledarc( $img, $cx, $cy, 2 * $r, 2 * $r, $start, $end, $color, IMG_ARC_PIE );
	}
}

/**
 * Rectangle arrondi avec contour (comme les icônes du thème : trait encre).
 *
 * @param \GdImage $img Image.
 */
function starter_demo_rrect_line( $img, float $x1, float $y1, float $x2, float $y2, float $r, int $fill, int $stroke, float $t ): void {
	starter_demo_rrect( $img, $x1, $y1, $x2, $y2, $r, $stroke );
	starter_demo_rrect( $img, $x1 + $t, $y1 + $t, $x2 - $t, $y2 - $t, max( 0, $r - $t ), $fill );
}

/**
 * Disque, avec contour facultatif.
 *
 * @param \GdImage $img Image.
 */
function starter_demo_circle( $img, float $cx, float $cy, float $r, int $fill, ?int $stroke = null, float $t = 0 ): void {
	if ( null !== $stroke ) {
		imagefilledellipse( $img, (int) $cx, (int) $cy, (int) ( 2 * $r ), (int) ( 2 * $r ), $stroke );
		$r -= $t;
	}
	imagefilledellipse( $img, (int) $cx, (int) $cy, (int) ( 2 * $r ), (int) ( 2 * $r ), $fill );
}

/**
 * Fond en dégradé vertical.
 *
 * @param \GdImage $img Image.
 */
function starter_demo_gradient( $img, int $w, int $h, string $from, string $to ): void {
	[ $r1, $g1, $b1 ] = sscanf( ltrim( $from, '#' ), '%02x%02x%02x' );
	[ $r2, $g2, $b2 ] = sscanf( ltrim( $to, '#' ), '%02x%02x%02x' );
	for ( $y = 0; $y < $h; $y++ ) {
		$t = $y / max( 1, $h - 1 );
		imageline( $img, 0, $y, $w, $y, imagecolorallocate( $img, (int) ( $r1 + ( $r2 - $r1 ) * $t ), (int) ( $g1 + ( $g2 - $g1 ) * $t ), (int) ( $b1 + ( $b2 - $b1 ) * $t ) ) );
	}
}

/**
 * Fenêtre de navigateur (fond blanc, barre avec trois pastilles).
 * Retourne la zone de contenu [x1, y1, x2, y2].
 *
 * @param \GdImage $img Image.
 * @return float[]
 */
function starter_demo_window( $img, float $x1, float $y1, float $x2, float $y2, float $u ): array {
	$c = starter_demo_colors();
	$t = 0.55 * $u;

	starter_demo_rrect_line( $img, $x1, $y1, $x2, $y2, 3 * $u, starter_demo_rgb( $img, $c['white'] ), starter_demo_rgb( $img, $c['ink'] ), $t );

	// Barre du haut + filet.
	$bar = $y1 + 7 * $u;
	imagefilledrectangle( $img, (int) $x1, (int) $bar, (int) $x2, (int) ( $bar + $t ), starter_demo_rgb( $img, $c['ink'] ) );
	foreach ( array( 'pink', 'lavender', 'line' ) as $i => $name ) {
		starter_demo_circle( $img, $x1 + ( 4.5 + 4 * $i ) * $u, $y1 + 3.6 * $u, 1.3 * $u, starter_demo_rgb( $img, $c[ $name ] ), starter_demo_rgb( $img, $c['ink'] ), 0.4 * $u );
	}
	// Barre d'adresse.
	starter_demo_rrect( $img, $x1 + 18 * $u, $y1 + 2.2 * $u, min( $x2 - 6 * $u, $x1 + 60 * $u ), $y1 + 5 * $u, 1.4 * $u, starter_demo_rgb( $img, $c['mist'] ) );

	return array( $x1 + 6 * $u, $bar + 6 * $u, $x2 - 6 * $u, $y2 - 5 * $u );
}

/**
 * Lignes de texte factices (barres arrondies).
 *
 * @param \GdImage $img    Image.
 * @param float[]  $widths Largeur de chaque ligne (0 à 1 de $w).
 * @return float Ordonnée sous la dernière ligne.
 */
function starter_demo_lines( $img, float $x, float $y, float $w, array $widths, float $height, float $gap, int $color ): float {
	foreach ( $widths as $ratio ) {
		starter_demo_rrect( $img, $x, $y, $x + $w * $ratio, $y + $height, $height / 2, $color );
		$y += $height + $gap;
	}
	return $y;
}

/*
 * ---------------------------------------------------------------------------
 * Scènes. Chaque fonction dessine sur $img (W × H, déjà suréchantillonnée).
 * $u = 1 % du plus petit côté : toutes les tailles en dépendent.
 * ---------------------------------------------------------------------------
 */

/**
 * Fenêtre de navigateur : page d'accueil d'un site vitrine.
 *
 * @param \GdImage $img Image.
 */
function starter_demo_scene_site( $img, int $w, int $h, float $u, int $seed ): void {
	$c = starter_demo_colors();
	starter_demo_gradient( $img, $w, $h, $c['lavender'], $c['lilac'] );
	starter_demo_circle( $img, $w * 0.86, $h * 0.14, 26 * $u, starter_demo_rgb( $img, $c['pink'], 30 ) );

	$ww                       = min( $w * 0.78, 150 * $u );
	[ $x1, $y1, $x2, $y2 ]    = starter_demo_window( $img, ( $w - $ww ) / 2, $h * 0.14, ( $w + $ww ) / 2, $h * 1.02, $u );
	$ink                      = starter_demo_rgb( $img, $c['ink'] );
	$grey                     = starter_demo_rgb( $img, $c['grey'] );
	$col                      = ( $x2 - $x1 ) * 0.52;

	// Menu.
	starter_demo_circle( $img, $x1 + 1.8 * $u, $y1 + 1.4 * $u, 1.8 * $u, starter_demo_rgb( $img, $c['lavender'] ) );
	foreach ( array( 0, 1, 2 ) as $i ) {
		starter_demo_rrect( $img, $x2 - ( 30 - 9 * $i ) * $u, $y1 + 0.6 * $u, $x2 - ( 24 - 9 * $i ) * $u, $y1 + 2.2 * $u, $u, $grey );
	}

	// Titre (dernier mot surligné), texte, bouton.
	$y = $y1 + 9 * $u;
	starter_demo_rrect( $img, $x1, $y, $x1 + $col * 0.92, $y + 5.5 * $u, 2.75 * $u, $ink );
	$y += 8 * $u;
	starter_demo_rrect( $img, $x1, $y, $x1 + $col * 0.4, $y + 5.5 * $u, 2.75 * $u, $ink );
	starter_demo_rrect( $img, $x1 + $col * 0.44, $y - 0.8 * $u, $x1 + $col * 0.86, $y + 6.3 * $u, 1.4 * $u, starter_demo_rgb( $img, $c['lavender'] ) );
	starter_demo_rrect( $img, $x1 + $col * 0.47, $y, $x1 + $col * 0.83, $y + 5.5 * $u, 2.75 * $u, $ink );
	$y  = starter_demo_lines( $img, $x1, $y + 11 * $u, $col, array( 0.9, 0.75, 0.5 ), 1.6 * $u, 1.8 * $u, $grey );
	$y += 2.5 * $u;
	starter_demo_rrect( $img, $x1, $y, $x1 + 24 * $u, $y + 6 * $u, 3 * $u, $ink );
	starter_demo_circle( $img, $x1 + 3 * $u, $y + 3 * $u, 2 * $u, starter_demo_rgb( $img, $c['plum'] ) );

	// Visuel à droite : soleil + collines dans un cadre arrondi.
	$vx1 = $x1 + $col + 6 * $u;
	$vy2 = min( $y2, $y1 + 50 * $u );
	starter_demo_rrect( $img, $vx1, $y1 + 8 * $u, $x2, $vy2, 2.5 * $u, starter_demo_rgb( $img, $c['mist'] ) );
	$vcx = ( $vx1 + $x2 ) / 2;
	starter_demo_circle( $img, $vcx + ( $seed % 2 ? 6 : -6 ) * $u, $y1 + 22 * $u, 7 * $u, starter_demo_rgb( $img, $c['pink'] ), $ink, 0.5 * $u );
	$fw = $x2 - $vx1;
	imagefilledellipse( $img, (int) ( $vcx - $fw * 0.12 ), (int) $vy2, (int) ( $fw * 0.72 ), (int) ( 26 * $u ), starter_demo_rgb( $img, $c['lilac'] ) );
	imagefilledellipse( $img, (int) ( $vcx + $fw * 0.16 ), (int) $vy2, (int) ( $fw * 0.64 ), (int) ( 18 * $u ), starter_demo_rgb( $img, $c['plum'] ) );
	// Recouvre ce qui dépasse sous le cadre.
	imagefilledrectangle( $img, (int) $vx1, (int) $vy2 + 1, (int) $x2, (int) ( $vy2 + 14 * $u ), starter_demo_rgb( $img, $c['white'] ) );
}

/**
 * Fenêtre de navigateur : boutique en ligne (grille de produits).
 *
 * @param \GdImage $img Image.
 */
function starter_demo_scene_shop( $img, int $w, int $h, float $u, int $seed ): void {
	$c = starter_demo_colors();
	starter_demo_gradient( $img, $w, $h, $c['mist'], $c['lavender'] );
	starter_demo_circle( $img, $w * 0.1, $h * 0.85, 30 * $u, starter_demo_rgb( $img, $c['lilac'], 60 ) );

	$ww                    = min( $w * 0.8, 160 * $u );
	[ $x1, $y1, $x2, $y2 ] = starter_demo_window( $img, ( $w - $ww ) / 2, $h * 0.12, ( $w + $ww ) / 2, $h * 1.02, $u );
	$ink                   = starter_demo_rgb( $img, $c['ink'] );
	$grey                  = starter_demo_rgb( $img, $c['grey'] );

	// Titre de la page + panier.
	starter_demo_rrect( $img, $x1, $y1, $x1 + 34 * $u, $y1 + 4.5 * $u, 2.25 * $u, $ink );
	starter_demo_circle( $img, $x2 - 2.5 * $u, $y1 + 2.2 * $u, 2.8 * $u, $ink );
	starter_demo_circle( $img, $x2 + 0.2 * $u, $y1 - 0.4 * $u, 1.3 * $u, starter_demo_rgb( $img, $c['pink'] ) );

	// Produits.
	$n      = ( $x2 - $x1 ) > 110 * $u ? 4 : 3;
	$gap    = 3 * $u;
	$cw     = ( $x2 - $x1 - ( $n - 1 ) * $gap ) / $n;
	$top    = $y1 + 9 * $u;
	$shapes = array( 'pink', 'lavender', 'plum', 'lilac' );
	for ( $i = 0; $i < $n; $i++ ) {
		$cx1 = $x1 + $i * ( $cw + $gap );
		starter_demo_rrect( $img, $cx1, $top, $cx1 + $cw, $top + $cw * 1.05, 2.5 * $u, starter_demo_rgb( $img, $c['mist'] ) );
		$color = starter_demo_rgb( $img, $c[ $shapes[ ( $i + $seed ) % 4 ] ] );
		$mid   = $cx1 + $cw / 2;
		$cy    = $top + $cw * 0.52;
		// Produit : flacon, boîte ou boule, en alternance.
		if ( 0 === ( $i + $seed ) % 3 ) {
			starter_demo_circle( $img, $mid, $cy, $cw * 0.26, $color, $ink, 0.5 * $u );
		} elseif ( 1 === ( $i + $seed ) % 3 ) {
			starter_demo_rrect_line( $img, $mid - $cw * 0.22, $cy - $cw * 0.24, $mid + $cw * 0.22, $cy + $cw * 0.28, 1.5 * $u, $color, $ink, 0.5 * $u );
		} else {
			starter_demo_rrect_line( $img, $mid - $cw * 0.07, $cy - $cw * 0.36, $mid + $cw * 0.07, $cy - $cw * 0.18, 0.8 * $u, starter_demo_rgb( $img, $c['white'] ), $ink, 0.5 * $u );
			starter_demo_rrect_line( $img, $mid - $cw * 0.18, $cy - $cw * 0.2, $mid + $cw * 0.18, $cy + $cw * 0.3, 4 * $u, $color, $ink, 0.5 * $u );
		}
		$ty = $top + $cw * 1.05 + 2.5 * $u;
		starter_demo_lines( $img, $cx1, $ty, $cw, array( 0.75, 0.45 ), 1.6 * $u, 1.6 * $u, $grey );
		starter_demo_rrect( $img, $cx1 + $cw - 11 * $u, $ty, $cx1 + $cw, $ty + 4.4 * $u, 2.2 * $u, $ink );
	}
}

/**
 * Identité visuelle : logo, nuancier, spécimen typographique.
 *
 * @param \GdImage $img Image.
 */
function starter_demo_scene_brand( $img, int $w, int $h, float $u, int $seed ): void {
	$c   = starter_demo_colors();
	$ink = starter_demo_rgb( $img, $c['ink'] );
	starter_demo_gradient( $img, $w, $h, $c['paper'], $c['mist'] );

	// La composition fait ≈ 150 unités de large : réduite pour tenir même recadrée en 4:3.
	$u = min( $u, $w * 0.75 / 165 );

	// Composition centrée (≈ 150 % de large) : logo, nuancier, « Aa ».
	$x0 = ( $w - 150 * $u ) / 2;
	$cx = $x0 + 26 * $u;
	$cy = $h * 0.5;

	// Logo : deux formes superposées (comme les icônes « Pourquoi nous »).
	starter_demo_rrect_line( $img, $cx - 26 * $u, $cy - 20 * $u, $cx + 6 * $u, $cy + 12 * $u, 3 * $u, starter_demo_rgb( $img, $c['pink'] ), $ink, 0.6 * $u );
	starter_demo_circle( $img, $cx + 4 * $u, $cy + 2 * $u, 20 * $u, starter_demo_rgb( $img, $c['lavender'], 25 ), null );
	imagesetthickness( $img, (int) max( 1, 0.6 * $u ) );
	imageellipse( $img, (int) ( $cx + 4 * $u ), (int) ( $cy + 2 * $u ), (int) ( 40 * $u ), (int) ( 40 * $u ), $ink );
	imagesetthickness( $img, 1 );

	// Nuancier.
	$sx = $x0 + 68 * $u;
	$sy = $cy - 24 * $u;
	foreach ( array( 'ink', 'plum', 'lavender', 'pink' ) as $i => $name ) {
		starter_demo_rrect_line( $img, $sx, $sy + $i * 12 * $u, $sx + 30 * $u, $sy + $i * 12 * $u + 9 * $u, 4.5 * $u, starter_demo_rgb( $img, $c[ $name ] ), $ink, 0.5 * $u );
	}

	// « Aa » si une police système est disponible.
	$fonts = array( '/System/Library/Fonts/Supplemental/Arial Bold.ttf', '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf' );
	$font  = current( array_filter( $fonts, 'is_file' ) );
	if ( $font && function_exists( 'imagettftext' ) ) {
		imagettftext( $img, 20 * $u, 0, (int) ( $sx + 36 * $u ), (int) ( $cy + 8 * $u ), $ink, $font, 'Aa' );
	}
}

/**
 * Composition abstraite : soleil, arche, carrés. Varie selon $seed ; fond foncé une fois sur trois.
 *
 * @param \GdImage $img Image.
 */
function starter_demo_scene_abstract( $img, int $w, int $h, float $u, int $seed ): void {
	$c    = starter_demo_colors();
	$dark = 2 === $seed % 3;
	$line = starter_demo_rgb( $img, $dark ? $c['lavender'] : $c['ink'] );

	$backgrounds = array( array( $c['lavender'], $c['mist'] ), array( $c['pink'], $c['lavender'] ), array( $c['plum'], $c['ink'] ) );
	[ $from, $to ] = $backgrounds[ $seed % 3 ];
	starter_demo_gradient( $img, $w, $h, $from, $to );

	$flip = $seed % 2 ? -1 : 1;
	$cx   = $w / 2;
	$cy   = $h / 2;

	// Arche (demi-disque) posée en bas.
	$ax = $cx - $flip * 22 * $u;
	imagefilledarc( $img, (int) $ax, (int) ( $h * 0.82 ), (int) ( 56 * $u ), (int) ( 56 * $u ), 180, 360, $line, IMG_ARC_PIE );
	imagefilledarc( $img, (int) $ax, (int) ( $h * 0.82 ), (int) ( 54.8 * $u ), (int) ( 54.8 * $u ), 180, 360, starter_demo_rgb( $img, $dark ? $c['plum'] : $c['lilac'] ), IMG_ARC_PIE );
	imagefilledrectangle( $img, 0, (int) ( $h * 0.82 ), $w, (int) ( $h * 0.82 + 0.6 * $u ), $line );

	// Soleil.
	starter_demo_circle( $img, $cx + $flip * 14 * $u, $cy - 12 * $u, 16 * $u, starter_demo_rgb( $img, $c['pink'], $dark ? 0 : 10 ), $line, 0.6 * $u );
	// Disque translucide qui chevauche le soleil.
	starter_demo_circle( $img, $cx + $flip * 26 * $u, $cy - 2 * $u, 13 * $u, starter_demo_rgb( $img, $c['lavender'], 55 ) );

	// Petit carré incliné à 0° (losange si seed pair).
	$sx = $cx + $flip * 38 * $u;
	$sy = $h * 0.82 - 10 * $u;
	if ( 0 === $seed % 2 ) {
		imagefilledpolygon( $img, array( (int) $sx, (int) ( $sy - 8 * $u ), (int) ( $sx + 8 * $u ), (int) $sy, (int) $sx, (int) ( $sy + 8 * $u ), (int) ( $sx - 8 * $u ), (int) $sy ), $line );
	} else {
		starter_demo_rrect_line( $img, $sx - 7 * $u, $sy - 7 * $u, $sx + 7 * $u, $sy + 7 * $u, 1.2 * $u, starter_demo_rgb( $img, $c['white'] ), $line, 0.6 * $u );
	}
}

/**
 * Personnage stylisé (tête, cheveux, épaules), centré en ($cx, $cy), hauteur ≈ 70 × $s.
 *
 * @param \GdImage $img Image.
 */
function starter_demo_person( $img, float $cx, float $cy, float $s, int $variant ): void {
	$c      = starter_demo_colors();
	$skins  = array( '#F2D3C2', '#C98E72', '#8A5A48', '#EBC3A8' );
	$hairs  = array( '#3E2A23', '#1C1C1C', '#1C1C1C', '#A0643C' );
	$cloths = array( $c['plum'], $c['ink'], $c['pink'], '#7C6FB3' );
	$v      = $variant % 4;
	$ink    = starter_demo_rgb( $img, $c['ink'] );

	// Épaules.
	imagefilledellipse( $img, (int) $cx, (int) ( $cy + 40 * $s ), (int) ( 66 * $s ), (int) ( 56 * $s ), starter_demo_rgb( $img, $cloths[ $v ] ) );
	// Cou + tête.
	starter_demo_rrect( $img, $cx - 5 * $s, $cy - 2 * $s, $cx + 5 * $s, $cy + 14 * $s, 3 * $s, starter_demo_rgb( $img, $skins[ $v ] ) );
	// Cheveux longs derrière la tête (variantes 0 et 2).
	if ( 0 === $v % 2 ) {
		starter_demo_rrect( $img, $cx - 15 * $s, $cy - 22 * $s, $cx + 15 * $s, $cy + 8 * $s, 14 * $s, starter_demo_rgb( $img, $hairs[ $v ] ) );
	}
	starter_demo_circle( $img, $cx, $cy - 10 * $s, 13 * $s, starter_demo_rgb( $img, $skins[ $v ] ) );
	// Cheveux sur le dessus.
	imagefilledarc( $img, (int) $cx, (int) ( $cy - 13 * $s ), (int) ( 28 * $s ), (int) ( 22 * $s ), 180, 360, starter_demo_rgb( $img, $hairs[ $v ] ), IMG_ARC_PIE );
	// Lunettes (variante 1).
	if ( 1 === $v ) {
		imagesetthickness( $img, (int) max( 1, 0.9 * $s ) );
		imageellipse( $img, (int) ( $cx - 5 * $s ), (int) ( $cy - 8 * $s ), (int) ( 8 * $s ), (int) ( 8 * $s ), $ink );
		imageellipse( $img, (int) ( $cx + 5 * $s ), (int) ( $cy - 8 * $s ), (int) ( 8 * $s ), (int) ( 8 * $s ), $ink );
		imagesetthickness( $img, 1 );
	}
}

/**
 * Portrait (avatar rond du bloc Appel à l'action).
 *
 * @param \GdImage $img Image.
 */
function starter_demo_scene_avatar( $img, int $w, int $h, float $u, int $seed ): void {
	$c   = starter_demo_colors();
	$bgs = array( array( $c['mist'], $c['lavender'] ), array( $c['pink'], $c['lavender'] ), array( $c['lavender'], $c['lilac'] ), array( $c['paper'], $c['pink'] ) );
	[ $from, $to ] = $bgs[ $seed % 4 ];
	starter_demo_gradient( $img, $w, $h, $from, $to );
	starter_demo_person( $img, $w / 2, $h * 0.52, $u * 1.15, $seed );
}

/**
 * Le studio : bureau avec ordinateur (maquette de site à l'écran), plante, mug,
 * moodboard au mur et fenêtre ronde. Tout tient dans les 75 % centraux en hauteur
 * (l'image est recadrée en bandeau dans le bloc Hero).
 *
 * @param \GdImage $img Image.
 */
function starter_demo_scene_studio( $img, int $w, int $h, float $u, int $seed ): void {
	$c    = starter_demo_colors();
	$ink  = starter_demo_rgb( $img, $c['ink'] );
	$t    = 0.55 * $u;
	$cx   = $w / 2;
	$cy   = $h / 2;
	$desk = $cy + 20 * $u; // Plateau du bureau.

	starter_demo_gradient( $img, $w, $h, $c['mist'], $c['lavender'] );

	// Fenêtre ronde (soleil) en haut à gauche.
	$wx = $cx - 66 * $u;
	$wy = $cy - 22 * $u;
	starter_demo_circle( $img, $wx, $wy, 13 * $u, starter_demo_rgb( $img, $c['white'] ), $ink, $t );
	starter_demo_circle( $img, $wx + 4 * $u, $wy + 4 * $u, 5.5 * $u, starter_demo_rgb( $img, $c['pink'] ) );
	imagefilledrectangle( $img, (int) ( $wx - $t / 2 ), (int) ( $wy - 13 * $u ), (int) ( $wx + $t / 2 ), (int) ( $wy + 13 * $u ), $ink );
	imagefilledrectangle( $img, (int) ( $wx - 13 * $u ), (int) ( $wy - $t / 2 ), (int) ( $wx + 13 * $u ), (int) ( $wy + $t / 2 ), $ink );

	// Moodboard au mur : nuancier + photo + notes.
	$mx1 = $cx + 38 * $u;
	$my1 = $cy - 34 * $u;
	starter_demo_rrect_line( $img, $mx1, $my1, $mx1 + 40 * $u, $my1 + 30 * $u, 2 * $u, starter_demo_rgb( $img, $c['white'] ), $ink, $t );
	foreach ( array( 'ink', 'plum', 'lavender', 'pink' ) as $k => $name ) {
		starter_demo_circle( $img, $mx1 + ( 6 + 6.5 * $k ) * $u, $my1 + 7 * $u, 2.6 * $u, starter_demo_rgb( $img, $c[ $name ] ), $ink, 0.4 * $u );
	}
	starter_demo_rrect( $img, $mx1 + 4 * $u, $my1 + 13 * $u, $mx1 + 20 * $u, $my1 + 26 * $u, 1.2 * $u, starter_demo_rgb( $img, $c['lilac'] ) );
	starter_demo_circle( $img, $mx1 + 12 * $u, $my1 + 18 * $u, 2.5 * $u, starter_demo_rgb( $img, $c['pink'] ) );
	starter_demo_lines( $img, $mx1 + 23 * $u, $my1 + 14 * $u, 13 * $u, array( 1, 0.8, 0.6 ), 1.4 * $u, 2 * $u, starter_demo_rgb( $img, $c['grey'] ) );

	// Bureau : plateau + pieds.
	starter_demo_rrect_line( $img, $cx - 90 * $u, $desk, $cx + 90 * $u, $desk + 4 * $u, 1.2 * $u, starter_demo_rgb( $img, $c['lilac'] ), $ink, $t );
	foreach ( array( -80, 77 ) as $x ) {
		imagefilledrectangle( $img, (int) ( $cx + $x * $u ), (int) ( $desk + 4 * $u ), (int) ( $cx + ( $x + 3 ) * $u ), (int) ( $desk + 30 * $u ), $ink );
	}

	// Ordinateur : écran avec une maquette de site, puis le clavier.
	$sx1 = $cx - 22 * $u;
	$sx2 = $cx + 28 * $u;
	$sy1 = $desk - 36 * $u;
	$sy2 = $desk - 4 * $u;
	starter_demo_rrect_line( $img, $sx1, $sy1, $sx2, $sy2, 2 * $u, starter_demo_rgb( $img, $c['white'] ), $ink, $t );
	$px = $sx1 + 5 * $u;
	$py = $sy1 + 5 * $u;
	starter_demo_rrect( $img, $px, $py, $px + 22 * $u, $py + 3.6 * $u, 1.8 * $u, $ink );
	starter_demo_rrect( $img, $px, $py + 5.5 * $u, $px + 9 * $u, $py + 9.1 * $u, 1.8 * $u, $ink );
	starter_demo_rrect( $img, $px + 10 * $u, $py + 5 * $u, $px + 20 * $u, $py + 9.6 * $u, 1 * $u, starter_demo_rgb( $img, $c['lavender'] ) );
	starter_demo_lines( $img, $px, $py + 13 * $u, 20 * $u, array( 1, 0.7 ), 1.2 * $u, 1.6 * $u, starter_demo_rgb( $img, $c['grey'] ) );
	starter_demo_rrect( $img, $px, $py + 19.5 * $u, $px + 11 * $u, $py + 22.5 * $u, 1.5 * $u, $ink );
	starter_demo_rrect( $img, $sx2 - 16 * $u, $py, $sx2 - 5 * $u, $sy2 - 5 * $u, 1.2 * $u, starter_demo_rgb( $img, $c['mist'] ) );
	starter_demo_circle( $img, $sx2 - 10.5 * $u, $py + 7 * $u, 3 * $u, starter_demo_rgb( $img, $c['pink'] ), $ink, 0.4 * $u );
	// Pied de l'écran + clavier.
	$mid = ( $sx1 + $sx2 ) / 2;
	imagefilledrectangle( $img, (int) ( $mid - 2 * $u ), (int) $sy2, (int) ( $mid + 2 * $u ), (int) $desk, $ink );
	starter_demo_rrect_line( $img, $mid - 11 * $u, $desk - 2 * $u, $mid + 11 * $u, $desk + $t, 1 * $u, starter_demo_rgb( $img, $c['white'] ), $ink, 0.45 * $u );

	// Mug.
	$gx = $cx + 42 * $u;
	starter_demo_circle( $img, $gx + 6 * $u, $desk - 6 * $u, 3.2 * $u, starter_demo_rgb( $img, $c['mist'] ), $ink, $t );
	starter_demo_rrect_line( $img, $gx - 5 * $u, $desk - 12 * $u, $gx + 6 * $u, $desk + $t, 1.5 * $u, starter_demo_rgb( $img, $c['pink'] ), $ink, $t );

	// Plante : feuilles rondes au-dessus d'un pot.
	$plx = $cx - 38 * $u;
	foreach ( array( array( -5, -22, 5.5, 'lilac' ), array( 5, -25, 6, 'plum' ), array( -1, -31, 6, 'lavender' ), array( 1, -15.5, 4.5, 'pink' ) ) as [ $dx, $dy, $r, $name ] ) {
		starter_demo_circle( $img, $plx + $dx * $u, $desk + $dy * $u, $r * $u, starter_demo_rgb( $img, $c[ $name ] ), $ink, $t );
	}
	starter_demo_rrect_line( $img, $plx - 7 * $u, $desk - 11 * $u, $plx + 7 * $u, $desk + $t, 2 * $u, starter_demo_rgb( $img, $c['white'] ), $ink, $t );
	imagefilledrectangle( $img, (int) ( $plx - 7 * $u ), (int) ( $desk - 7.5 * $u ), (int) ( $plx + 7 * $u ), (int) ( $desk - 7.5 * $u + $t ), $ink );
}

/**
 * Génère une illustration et l'ajoute à la médiathèque.
 *
 * @param string $title  Titre (et texte alternatif) de l'image.
 * @param string $scene  site, shop, brand, abstract, avatar, studio.
 * @param int    $width  Largeur finale en px.
 * @param int    $height Hauteur finale en px.
 * @param int    $seed   Variante (couleurs, sens des formes…).
 * @return int ID de l'image, 0 en cas d'échec.
 */
function starter_demo_image( string $title, string $scene = 'abstract', int $width = 1600, int $height = 1000, int $seed = 0 ): int {
	$draw = 'starter_demo_scene_' . $scene;
	if ( ! function_exists( 'imagecreatetruecolor' ) || ! function_exists( $draw ) ) {
		return 0;
	}

	$w   = $width * STARTER_DEMO_SCALE;
	$h   = $height * STARTER_DEMO_SCALE;
	$img = imagecreatetruecolor( $w, $h );
	imagealphablending( $img, true );
	$draw( $img, $w, $h, min( $w, $h ) / 100, $seed );

	// Réduction à la taille finale : lisse les bords.
	$out = imagecreatetruecolor( $width, $height );
	imagecopyresampled( $out, $img, 0, 0, 0, 0, $width, $height, $w, $h );
	imagedestroy( $img );

	$tmp = wp_tempnam( sanitize_title( $title ) . '.jpg' );
	imagejpeg( $out, $tmp, 86 );
	imagedestroy( $out );

	$id = media_handle_sideload(
		array(
			'name'     => 'demo-' . sanitize_title( $title ) . '.jpg',
			'tmp_name' => $tmp,
		),
		0,
		$title
	);

	if ( is_wp_error( $id ) ) {
		WP_CLI::warning( 'Image « ' . $title . ' » : ' . $id->get_error_message() );
		return 0;
	}

	update_post_meta( $id, '_wp_attachment_image_alt', $title );
	return starter_demo_mark( $id );
}
