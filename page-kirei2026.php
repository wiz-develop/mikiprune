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

get_header();

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

$guest_name    = kirei2026_cfs_value( 'kirei_guest_name' );
$guest_profile = kirei2026_cfs_value( 'kirei_guest_profile' );
$guest_image   = kirei2026_image_url( kirei2026_cfs_value( 'kirei_guest_image' ) );
$show_guest    = '' !== trim( (string) $guest_name ) || '' !== trim( (string) $guest_profile ) || '' !== $guest_image;

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
			<a class="kirei2026-scroll-cue" href="#kirei2026-schedule">
				<span>Event information</span>
				<i aria-hidden="true"></i>
			</a>
		</div>
	</section>

	<section class="kirei2026-schedule" id="kirei2026-schedule" data-kirei-reveal>
		<div class="kirei2026-container">
			<header class="kirei2026-section-heading">
				<p>Schedule &amp; Venue</p>
				<h2><?php echo esc_html( kirei2026_cfs_value( 'kirei_schedule_heading', '日時・開催場所' ) ); ?></h2>
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
							'access_url'       => '',
							'floor_map_url'    => '',
							'timetable_url'    => '',
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
						<?php if ( $schedule['access_url'] || $schedule['floor_map_url'] || $schedule['timetable_url'] ) : ?>
							<nav class="kirei2026-date-card__links" aria-label="<?php echo esc_attr( $schedule['schedule_area'] ); ?>のご案内">
								<?php if ( $schedule['access_url'] ) : ?><a href="<?php echo esc_url( $schedule['access_url'] ); ?>">アクセス</a><?php endif; ?>
								<?php if ( $schedule['floor_map_url'] ) : ?><a href="<?php echo esc_url( $schedule['floor_map_url'] ); ?>">フロアマップ</a><?php endif; ?>
								<?php if ( $schedule['timetable_url'] ) : ?><a href="<?php echo esc_url( $schedule['timetable_url'] ); ?>">タイムスケジュール</a><?php endif; ?>
							</nav>
						<?php endif; ?>
					</article>
				<?php endforeach; ?>
			</div>

			<p class="kirei2026-note"><?php echo esc_html( kirei2026_cfs_value( 'kirei_schedule_note', '※詳細は準備が整い次第、お知らせいたします。' ) ); ?></p>
		</div>
	</section>

	<?php if ( $show_later_sections ) : ?>
	<section class="kirei2026-program" data-kirei-reveal>
		<div class="kirei2026-container">
			<header class="kirei2026-section-heading kirei2026-section-heading--light">
				<p>Three experiences</p>
				<h2><?php echo esc_html( kirei2026_cfs_value( 'kirei_program_heading', '開催内容' ) ); ?></h2>
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
					?>
					<article class="kirei2026-program-card <?php echo esc_attr( kirei2026_accent_class( $program['program_color'] ) ); ?>">
						<div class="kirei2026-program-card__media">
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

			<p class="kirei2026-note kirei2026-note--light"><?php echo esc_html( kirei2026_cfs_value( 'kirei_program_note', '※掲載の画像・イベント内容・構成はイメージです。実際の内容とは異なる場合があります。' ) ); ?></p>
		</div>
	</section>

	<?php if ( $show_guest ) : ?>
		<section class="kirei2026-guest" data-kirei-reveal>
			<div class="kirei2026-container kirei2026-guest__inner">
				<?php if ( $guest_image ) : ?>
					<div class="kirei2026-guest__image"><img src="<?php echo esc_url( $guest_image ); ?>" alt="<?php echo esc_attr( $guest_name ); ?>" loading="lazy"></div>
				<?php endif; ?>
				<div class="kirei2026-guest__body">
					<p class="kirei2026-guest__label">Talk guest</p>
					<h2><?php echo esc_html( kirei2026_cfs_value( 'kirei_guest_heading', '出演者プロフィール' ) ); ?></h2>
					<?php if ( $guest_name ) : ?><h3><?php echo esc_html( $guest_name ); ?></h3><?php endif; ?>
					<?php $guest_role = kirei2026_cfs_value( 'kirei_guest_role' ); ?>
					<?php if ( $guest_role ) : ?><p class="kirei2026-guest__role"><?php echo esc_html( $guest_role ); ?></p><?php endif; ?>
					<div class="kirei2026-guest__profile"><?php echo wp_kses_post( wpautop( $guest_profile ) ); ?></div>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<section class="kirei2026-closing" aria-label="Kirei 2026 メッセージ">
		<p><?php echo esc_html( kirei2026_cfs_value( 'kirei_closing_message', 'あなたらしい「キレイ」に、出会える一日を。' ) ); ?></p>
		<span>Beauty of MIKI EXELAND</span>
	</section>
	<?php endif; ?>
</main>

<?php get_footer(); ?>
