<?php
/**
 * Template Name: Kirei 2026
 *
 * @package WordPress
 * @subpackage Twenty_Nineteen
 */

$kirei2026_asset_version = wp_get_theme()->get( 'Version' );
$kirei2026_css_path      = get_stylesheet_directory() . '/assets/css/kirei2026.css';
$kirei2026_js_path       = get_stylesheet_directory() . '/assets/js/kirei2026.js';

if ( file_exists( $kirei2026_css_path ) ) {
	$kirei2026_asset_version = (string) filemtime( $kirei2026_css_path );
}

wp_enqueue_style(
	'kirei2026',
	get_stylesheet_directory_uri() . '/assets/css/kirei2026.css',
	array(),
	$kirei2026_asset_version
);

wp_enqueue_script(
	'kirei2026',
	get_stylesheet_directory_uri() . '/assets/js/kirei2026.js',
	array(),
	file_exists( $kirei2026_js_path ) ? (string) filemtime( $kirei2026_js_path ) : $kirei2026_asset_version,
	true
);

// 既存header.phpがトップページ固定のcanonicalを出力するため、
// このページではその1行だけを除去し、WordPress標準のcanonicalを残します。
ob_start();
get_header();
$kirei2026_header_html = ob_get_clean();
$kirei2026_home_canonical = sprintf(
	'<link rel="canonical" href="%s">',
	esc_url( home_url( '/' ) )
);
echo str_replace( $kirei2026_home_canonical, '', $kirei2026_header_html ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

if ( ! function_exists( 'kirei2026_cfs_value' ) ) {
	function kirei2026_cfs_value( $field_name, $default = '' ) {
		if ( function_exists( 'CFS' ) ) {
			$value = CFS()->get( $field_name );

			if ( is_array( $value ) ) {
				return empty( $value ) ? $default : $value;
			}

			if ( '' !== trim( (string) $value ) ) {
				return $value;
			}
		}

		return $default;
	}
}

if ( ! function_exists( 'kirei2026_image_url' ) ) {
	function kirei2026_image_url( $image, $default = '' ) {
		if ( is_numeric( $image ) ) {
			$url = wp_get_attachment_image_url( (int) $image, 'full' );
			return $url ? $url : $default;
		}

		if ( is_array( $image ) ) {
			if ( ! empty( $image['url'] ) ) {
				return $image['url'];
		}

			if ( ! empty( $image['ID'] ) ) {
				$url = wp_get_attachment_image_url( (int) $image['ID'], 'full' );
				return $url ? $url : $default;
			}
		}

		return '' !== trim( (string) $image ) ? (string) $image : $default;
	}
}

if ( ! function_exists( 'kirei2026_accent_class' ) ) {
	function kirei2026_accent_class( $accent ) {
		if ( is_array( $accent ) ) {
			$accent_keys = array_keys( $accent );
			$accent      = reset( $accent_keys );
		}

		$allowed = array( 'rose', 'green', 'blue' );
		return in_array( $accent, $allowed, true ) ? 'is-' . $accent : 'is-rose';
	}
}

if ( ! function_exists( 'kirei2026_versioned_asset_url' ) ) {
	function kirei2026_versioned_asset_url( $relative_path ) {
		$relative_path = '/' . ltrim( $relative_path, '/' );
		$file_path     = get_stylesheet_directory() . $relative_path;
		$file_url      = get_stylesheet_directory_uri() . $relative_path;

		return file_exists( $file_path ) ? add_query_arg( 'ver', (string) filemtime( $file_path ), $file_url ) : $file_url;
	}
}

if ( ! function_exists( 'kirei2026_closing_message_html' ) ) {
	function kirei2026_closing_message_html( $message ) {
		$message = esc_html( (string) $message );
		$message = str_replace(
			'時間（とき）',
			'<ruby>時<rt>と</rt></ruby><ruby>間<rt>き</rt></ruby>',
			$message
		);

		return wp_kses(
			$message,
			array(
				'ruby' => array(),
				'rt'   => array(),
			)
		);
	}
}

if ( ! function_exists( 'kirei2026_timetable_items' ) ) {
	function kirei2026_timetable_items( $value ) {
		$items = array();
		$lines = preg_split( '/\R/u', trim( (string) $value ) );

		foreach ( (array) $lines as $line ) {
			$line = trim( $line );
			if ( '' === $line ) {
				continue;
			}

			$parts   = preg_split( '/\s*[|｜]\s*/u', $line, 2 );
			$items[] = array(
				'time'  => trim( $parts[0] ),
				'title' => isset( $parts[1] ) ? trim( $parts[1] ) : '',
			);
		}

		return $items;
	}
}

$default_schedules = array(
	array(
		'schedule_area'    => '横浜会場',
		'schedule_date'    => '12.3',
		'schedule_weekday' => '木',
		'schedule_venue'   => 'パシフィコ横浜 マリンロビー',
	),
	array(
		'schedule_area'    => '大阪会場',
		'schedule_date'    => '12.8',
		'schedule_weekday' => '火',
		'schedule_venue'   => 'グランキューブ大阪 メインホワイエ',
	),
	array(
		'schedule_area'    => '福岡会場',
		'schedule_date'    => '12.15',
		'schedule_weekday' => '火',
		'schedule_venue'   => '福岡サンパレス 大ホール',
	),
);

$asset_base = get_stylesheet_directory_uri() . '/assets/image/kirei2026/';
$default_programs = array(
	array(
		'program_keyword'     => 'みる',
		'program_lead'        => 'キレイはここからはじまる',
		'program_title'       => 'メイクアップショーステージ',
		'program_description' => 'さまざまなシチュエーションを想定したメイクデモンストレーション。化粧品の使い方のコツもお伝えします。',
		'program_image'       => kirei2026_versioned_asset_url( '/assets/image/kirei2026/program-see.jpg' ),
		'program_image_alt'   => 'メイクアップショーのイメージ',
		'program_color'       => 'rose',
	),
	array(
		'program_keyword'     => 'きく',
		'program_lead'        => 'キレイのヒントがここにある',
		'program_title'       => '美容トークショー「〜輝け、新しい私〜」',
		'program_description' => '美しさを育むヒントや、年齢を重ねることを前向きに楽しむための考え方などをお届けします。',
		'program_image'       => kirei2026_versioned_asset_url( '/assets/image/kirei2026/program-listen.jpg' ),
		'program_image_alt'   => '美容トークショーのイメージ',
		'program_color'       => 'green',
	),
	array(
		'program_keyword'     => 'ふれる',
		'program_lead'        => 'キレイを手に入れる',
		'program_title'       => 'タッチアップブース',
		'program_description' => 'スキンケアからベースメイクまで、ミキの化粧品を見て、触れて、お試しいただけます。',
		'program_image'       => kirei2026_versioned_asset_url( '/assets/image/kirei2026/program-touch.jpg' ),
		'program_image_alt'   => 'タッチアップブースのイメージ',
		'program_color'       => 'blue',
	),
);

$schedules = kirei2026_cfs_value( 'kirei_schedule_rows', $default_schedules );
$programs  = kirei2026_cfs_value( 'kirei_program_rows', $default_programs );
$people    = (array) kirei2026_cfs_value( 'kirei_people_rows', array() );
$schedule_note = kirei2026_cfs_value( 'kirei_schedule_note' );
$schedule_heading    = (string) kirei2026_cfs_value( 'kirei_schedule_heading', '日時・開催場所' );
$venue_guide_heading = (string) kirei2026_cfs_value( 'kirei_venue_guide_heading', '会場案内・タイムスケジュール' );
$program_heading     = (string) kirei2026_cfs_value( 'kirei_program_heading', '開催内容' );
$people_heading      = (string) kirei2026_cfs_value( 'kirei_people_heading', '出演者' );
$venue_guide_heading_parts = explode( '・', $venue_guide_heading, 2 );

$has_program_content = false;
foreach ( (array) $programs as $program ) {
	foreach ( array( 'program_keyword', 'program_lead', 'program_title', 'program_description', 'program_image' ) as $field_name ) {
		if ( ! empty( $program[ $field_name ] ) ) {
			$has_program_content = true;
			break 2;
		}
	}
}

if ( ! $has_program_content ) {
	$programs = $default_programs;
}

$has_venue_details = false;
foreach ( (array) $schedules as $schedule ) {
	foreach ( array( 'floor_map_image', 'see_schedule', 'listen_schedule', 'touch_schedule' ) as $field_name ) {
		if ( ! empty( $schedule[ $field_name ] ) ) {
			$has_venue_details = true;
			break 2;
		}
	}
}

$has_people = false;
foreach ( $people as $person ) {
	if ( ! empty( $person['person_name'] ) || ! empty( $person['person_profile'] ) || ! empty( $person['person_image'] ) ) {
		$has_people = true;
		break;
	}
}

// 開催内容以降はテスト環境で確認し、本番では公開準備が整うまで非表示にします。
$show_later_sections = 'mikiprune-2022renewal.3d-showcase.net' === wp_parse_url( home_url( '/' ), PHP_URL_HOST );
?>

<main class="kirei2026" id="main-content">
	<section class="kirei2026-hero" aria-labelledby="kirei2026-title">
		<div class="kirei2026-petal kirei2026-petal--one" aria-hidden="true"></div>
		<div class="kirei2026-petal kirei2026-petal--two" aria-hidden="true"></div>
		<div class="kirei2026-hero__inner">
			<h1 class="kirei2026-hero__title" id="kirei2026-title">
				<img src="<?php echo esc_url( $asset_base . 'lirei2026-logo.png' ); ?>" alt="Beauty of MIKI EXELAND Kirei 2026 キレイに出会うと、自分をもっと好きになる。">
			</h1>
			<a class="kirei2026-scroll-cue" href="#kirei2026-guide">
				<span>Event information</span>
				<i aria-hidden="true"></i>
			</a>
		</div>
	</section>

	<section class="kirei2026-guide" id="kirei2026-guide" aria-labelledby="kirei2026-guide-title">
		<div class="kirei2026-container">
			<header class="kirei2026-guide__heading">
				<h2 id="kirei2026-guide-title">イベント情報</h2>
				<p>Kirei 2026の開催情報をご案内します。</p>
			</header>
			<nav class="kirei2026-guide__nav" aria-label="ページ内メニュー">
				<ul>
					<li><a href="#kirei2026-schedule"><span><?php echo esc_html( $schedule_heading ); ?></span><i aria-hidden="true"></i></a></li>
					<?php if ( $show_later_sections && $has_venue_details ) : ?>
						<li>
							<a href="#kirei2026-venue-guide">
								<span>
									<?php echo esc_html( $venue_guide_heading_parts[0] ); ?><?php if ( isset( $venue_guide_heading_parts[1] ) ) : ?>・<wbr><span class="kirei2026-guide__nav-tail"><?php echo esc_html( $venue_guide_heading_parts[1] ); ?></span><?php endif; ?>
								</span>
								<i aria-hidden="true"></i>
							</a>
						</li>
					<?php endif; ?>
					<?php if ( $show_later_sections ) : ?>
						<li><a href="#kirei2026-program"><span><?php echo esc_html( $program_heading ); ?></span><i aria-hidden="true"></i></a></li>
					<?php endif; ?>
					<?php if ( $show_later_sections && $has_people ) : ?>
						<li><a href="#kirei2026-people"><span><?php echo esc_html( $people_heading ); ?></span><i aria-hidden="true"></i></a></li>
					<?php endif; ?>
				</ul>
			</nav>
		</div>
	</section>

	<section class="kirei2026-schedule" id="kirei2026-schedule" data-kirei-reveal>
		<div class="kirei2026-container">
			<header class="kirei2026-section-heading">
				<p>Schedule &amp; Venue</p>
				<h2><?php echo esc_html( $schedule_heading ); ?></h2>
			</header>

			<div class="kirei2026-schedule__list">
				<?php foreach ( $schedules as $index => $schedule ) : ?>
					<?php
					$schedule = wp_parse_args(
						$schedule,
						array(
							'schedule_area'    => '',
							'schedule_date'    => '',
							'schedule_weekday' => '',
							'schedule_time'    => '',
							'schedule_venue'   => '',
						)
					);
					?>
					<article class="kirei2026-date-card">
						<div class="kirei2026-date-card__number" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $index + 1 ) ); ?></div>
						<div class="kirei2026-date-card__place">
							<h3><?php echo esc_html( $schedule['schedule_area'] ); ?></h3>
							<p><span>会場</span><?php echo esc_html( $schedule['schedule_venue'] ); ?></p>
						</div>
						<div class="kirei2026-date-card__schedule">
							<p class="kirei2026-date-card__date">
								<strong><?php echo esc_html( $schedule['schedule_date'] ); ?></strong>
								<span>（<?php echo esc_html( $schedule['schedule_weekday'] ); ?>）</span>
							</p>
							<?php if ( '' !== trim( (string) $schedule['schedule_time'] ) ) : ?>
								<p class="kirei2026-date-card__time"><?php echo esc_html( $schedule['schedule_time'] ); ?></p>
							<?php endif; ?>
						</div>
					</article>
				<?php endforeach; ?>
			</div>

			<?php if ( '' !== trim( (string) $schedule_note ) ) : ?>
				<p class="kirei2026-note"><?php echo esc_html( $schedule_note ); ?></p>
			<?php endif; ?>
		</div>
	</section>

	<?php if ( $show_later_sections ) : ?>
	<?php if ( $has_venue_details ) : ?>
	<section class="kirei2026-venue-guide" id="kirei2026-venue-guide" data-kirei-reveal>
		<div class="kirei2026-container">
			<header class="kirei2026-section-heading">
				<p>Venue guide</p>
				<h2>
					<?php
					echo esc_html( $venue_guide_heading_parts[0] );
					if ( isset( $venue_guide_heading_parts[1] ) ) {
						echo '・<wbr><span class="kirei2026-venue-guide__title-tail">' . esc_html( $venue_guide_heading_parts[1] ) . '</span>';
					}
					?>
				</h2>
			</header>
			<p class="kirei2026-venue-guide__instruction">会場を選択すると、フロアマップとタイムスケジュールを確認できます。</p>

			<div class="kirei2026-venue-guide__list">
				<?php foreach ( (array) $schedules as $index => $schedule ) : ?>
					<?php
					$schedule = wp_parse_args(
						$schedule,
						array(
							'schedule_area'    => '',
							'schedule_date'    => '',
							'schedule_weekday' => '',
							'floor_map_image'  => '',
							'floor_map_alt'    => '',
							'floor_map_caption'=> '',
							'see_schedule'     => '',
							'listen_schedule'  => '',
							'touch_schedule'   => '',
						)
					);
					$floor_map_image_url = kirei2026_image_url( $schedule['floor_map_image'] );
					$see_items     = kirei2026_timetable_items( $schedule['see_schedule'] );
					$listen_items  = kirei2026_timetable_items( $schedule['listen_schedule'] );
					$touch_items   = kirei2026_timetable_items( $schedule['touch_schedule'] );

					if ( ! $floor_map_image_url && ! $see_items && ! $listen_items && ! $touch_items ) {
						continue;
					}
					?>
					<details class="kirei2026-venue">
						<summary>
							<span><?php echo esc_html( $schedule['schedule_area'] ); ?></span>
							<small><?php echo esc_html( $schedule['schedule_date'] ); ?>（<?php echo esc_html( $schedule['schedule_weekday'] ); ?>）</small>
							<i aria-hidden="true"></i>
						</summary>
						<div class="kirei2026-venue__content">
							<?php if ( $floor_map_image_url ) : ?>
								<figure class="kirei2026-floor-map">
									<p class="kirei2026-floor-map__label">フロアイメージ</p>
									<img src="<?php echo esc_url( $floor_map_image_url ); ?>" alt="<?php echo esc_attr( $schedule['floor_map_alt'] ); ?>" loading="lazy">
									<?php if ( $schedule['floor_map_caption'] ) : ?><figcaption><?php echo esc_html( $schedule['floor_map_caption'] ); ?></figcaption><?php endif; ?>
								</figure>
							<?php endif; ?>

							<div class="kirei2026-timetable" aria-label="<?php echo esc_attr( $schedule['schedule_area'] ); ?>のタイムスケジュール">
								<?php
								$tracks = array(
									array( 'keyword' => 'みる', 'label' => 'メイクアップショー', 'class' => 'is-see', 'items' => $see_items ),
									array( 'keyword' => 'きく', 'label' => 'トークショー', 'class' => 'is-listen', 'items' => $listen_items ),
									array( 'keyword' => 'ふれる', 'label' => '商品展示・タッチアップ', 'class' => 'is-touch', 'items' => $touch_items ),
								);
								?>
								<?php foreach ( $tracks as $track ) : ?>
									<?php if ( empty( $track['items'] ) ) { continue; } ?>
									<section class="kirei2026-timetable__track <?php echo esc_attr( $track['class'] ); ?>">
										<header><strong><?php echo esc_html( $track['keyword'] ); ?></strong><span><?php echo esc_html( $track['label'] ); ?></span></header>
										<ul>
											<?php foreach ( $track['items'] as $item ) : ?>
												<li><time><?php echo esc_html( $item['time'] ); ?></time><?php if ( $item['title'] ) : ?><span><?php echo esc_html( $item['title'] ); ?></span><?php endif; ?></li>
											<?php endforeach; ?>
										</ul>
									</section>
								<?php endforeach; ?>
							</div>
						</div>
					</details>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<section class="kirei2026-program" id="kirei2026-program" data-kirei-reveal>
		<div class="kirei2026-container">
			<header class="kirei2026-section-heading kirei2026-section-heading--light">
				<p>Three experiences</p>
				<h2><?php echo esc_html( $program_heading ); ?></h2>
			</header>

			<div class="kirei2026-program__list">
				<?php foreach ( $programs as $index => $program ) : ?>
					<?php
					$program = wp_parse_args(
						$program,
						array(
							'program_keyword'     => '',
							'program_lead'        => '',
							'program_title'       => '',
							'program_description' => '',
							'program_image'       => '',
							'program_image_alt'   => '',
							'program_color'       => array( 'rose', 'green', 'blue' )[ $index % 3 ],
						)
					);
					$image_url = kirei2026_image_url( $program['program_image'] );
					$image_path = $image_url ? wp_parse_url( $image_url, PHP_URL_PATH ) : '';
					$media_class = 'kirei2026-program-card__media';
					if ( 'program-touch.jpg' === wp_basename( $image_path ) ) {
						$media_class .= ' is-contain';
					}
					?>
					<article class="kirei2026-program-card <?php echo esc_attr( kirei2026_accent_class( $program['program_color'] ) ); ?>">
						<div class="<?php echo esc_attr( $media_class ); ?>">
							<?php if ( $image_url ) : ?>
								<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $program['program_image_alt'] ); ?>" loading="lazy">
							<?php endif; ?>
							<span><?php echo esc_html( sprintf( '%02d', $index + 1 ) ); ?></span>
						</div>
						<div class="kirei2026-program-card__body">
							<p class="kirei2026-program-card__lead"><?php echo esc_html( $program['program_lead'] ); ?></p>
							<h3><?php echo esc_html( $program['program_keyword'] ); ?></h3>
							<h4><?php echo esc_html( $program['program_title'] ); ?></h4>
							<p class="kirei2026-program-card__description"><?php echo nl2br( esc_html( $program['program_description'] ) ); ?></p>
						</div>
					</article>
				<?php endforeach; ?>
			</div>

			<?php if ( $has_people ) : ?>
				<section class="kirei2026-people" id="kirei2026-people" aria-labelledby="kirei2026-people-title">
					<header class="kirei2026-people__heading">
						<p>Artists &amp; guests</p>
						<h2 id="kirei2026-people-title"><?php echo esc_html( $people_heading ); ?></h2>
					</header>
					<div class="kirei2026-people__list">
						<?php foreach ( $people as $person ) : ?>
							<?php
							$person = wp_parse_args(
								$person,
								array(
									'person_program'  => 'see',
									'person_label'    => '',
									'person_role'     => '',
									'person_name'     => '',
									'person_profile'  => '',
									'person_image'    => '',
									'person_image_alt'=> '',
								)
							);
							$person_program = $person['person_program'];
							if ( is_array( $person_program ) ) {
								$program_keys   = array_keys( $person_program );
								$first_key      = reset( $program_keys );
								$person_program = is_int( $first_key ) ? reset( $person_program ) : $first_key;
							}
							$person_image_url = kirei2026_image_url( $person['person_image'] );
							$person_class     = 'listen' === $person_program ? 'is-listen' : 'is-see';
							?>
							<article class="kirei2026-person <?php echo esc_attr( $person_class ); ?>">
								<?php if ( $person_image_url ) : ?><div class="kirei2026-person__image"><img src="<?php echo esc_url( $person_image_url ); ?>" alt="<?php echo esc_attr( $person['person_image_alt'] ); ?>" loading="lazy"></div><?php endif; ?>
								<div class="kirei2026-person__body">
									<p class="kirei2026-person__program"><?php echo esc_html( 'listen' === $person_program ? 'きく' : 'みる' ); ?></p>
									<?php if ( $person['person_label'] ) : ?><p class="kirei2026-person__label"><?php echo esc_html( $person['person_label'] ); ?></p><?php endif; ?>
									<?php if ( $person['person_role'] ) : ?><p class="kirei2026-person__role"><?php echo esc_html( $person['person_role'] ); ?></p><?php endif; ?>
									<h3><?php echo esc_html( $person['person_name'] ); ?><small>さん</small></h3>
									<p class="kirei2026-person__profile"><?php echo nl2br( esc_html( $person['person_profile'] ) ); ?></p>
								</div>
							</article>
						<?php endforeach; ?>
					</div>
				</section>
			<?php endif; ?>

			<p class="kirei2026-note kirei2026-note--light"><?php echo esc_html( kirei2026_cfs_value( 'kirei_program_note', '※掲載の画像・イベント内容・構成はイメージです。実際の内容とは異なる場合があります。' ) ); ?></p>
		</div>
	</section>

	<section class="kirei2026-closing" aria-label="Kirei 2026 メッセージ">
		<p><?php echo kirei2026_closing_message_html( kirei2026_cfs_value( 'kirei_closing_message', 'キレイがきっと見つかる特別な時間（とき）' ) ); ?></p>
		<span>Beauty of MIKI EXELAND</span>
	</section>
	<?php endif; ?>
</main>

<?php get_footer(); ?>
