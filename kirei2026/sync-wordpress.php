<?php
/**
 * Kirei 2026 WordPress/CFS synchronizer.
 *
 * Usage:
 * php sync-wordpress.php --wp-load=/absolute/path/to/wp-load.php [--apply]
 */

if ( 'cli' !== PHP_SAPI ) {
	http_response_code( 403 );
	exit( "CLI only\n" );
}

$apply        = in_array( '--apply', $argv, true );
$wp_load_path = '';

foreach ( $argv as $argument ) {
	if ( 0 === strpos( $argument, '--wp-load=' ) ) {
		$wp_load_path = substr( $argument, strlen( '--wp-load=' ) );
		break;
	}
}

if ( ! $wp_load_path || ! is_file( $wp_load_path ) ) {
	fwrite( STDERR, "A valid --wp-load=/absolute/path/to/wp-load.php is required.\n" );
	exit( 1 );
}

require_once $wp_load_path;

if ( ! function_exists( 'CFS' ) ) {
	fwrite( STDERR, "Custom Field Suite is not active.\n" );
	exit( 1 );
}

function kirei2026_sync_report( $data ) {
	echo wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . PHP_EOL;
}

function kirei2026_sync_field_schema( $page_id ) {
	$fields  = array();
	$next_id = 1;

	$add_field = function ( $name, $label, $type, $parent_id, $notes, $options ) use ( &$fields, &$next_id ) {
		$id       = $next_id++;
		$fields[] = array(
			'id'        => $id,
			'name'      => $name,
			'label'     => $label,
			'type'      => $type,
			'notes'     => $notes,
			'parent_id' => (int) $parent_id,
			'weight'    => count( $fields ),
			'options'   => $options,
		);
		return $id;
	};

	$add_field( 'kirei_schedule_heading', '日時・開催場所：見出し', 'text', 0, '', array() );
	$schedule_loop = $add_field(
		'kirei_schedule_rows',
		'会場一覧',
		'loop',
		0,
		'',
		array(
			'row_display' => 0,
			'row_label'   => '会場',
			'button_label'=> '会場を追加',
			'limit_min'   => '',
			'limit_max'   => '',
		)
	);
	$add_field( 'schedule_area', '会場名', 'text', $schedule_loop, '', array() );
	$add_field( 'schedule_date', '開催日', 'text', $schedule_loop, '例：12.3', array() );
	$add_field( 'schedule_weekday', '曜日', 'text', $schedule_loop, '例：木', array() );
	$add_field( 'schedule_time', '時間', 'text', $schedule_loop, '例：10:00〜12:00', array() );
	$add_field( 'schedule_venue', '会場', 'text', $schedule_loop, '', array() );
	$add_field(
		'floor_map_image',
		'フロアマップ画像',
		'file',
		$schedule_loop,
		'会場ごとのフロアマップ。空欄の場合は会場案内に画像を表示しません。',
		array(
			'file_type'    => 'image',
			'return_value' => 'url',
		)
	);
	$add_field( 'floor_map_alt', 'フロアマップ画像の説明', 'text', $schedule_loop, '例：横浜会場のフロアマップ', array() );
	$add_field( 'floor_map_caption', 'フロアマップ凡例', 'text', $schedule_loop, '例：A：みる／B：きく／C：ふれる', array() );
	$add_field(
		'combine_see_listen',
		'「みる・きく」の表示方法',
		'select',
		$schedule_loop,
		'大阪・福岡会場のように同じステージで交互に開催する場合は「合同表示」を選択します。',
		array(
			'choices'      => array(
				'separate' => '個別表示',
				'combined' => '合同表示',
			),
			'force_single' => 1,
		)
	);
	$add_field( 'see_schedule', '「みる」タイムスケジュール', 'textarea', $schedule_loop, '1行につき「時間｜内容」の形式で入力します。', array() );
	$add_field( 'listen_schedule', '「きく」タイムスケジュール', 'textarea', $schedule_loop, '1行につき「時間｜内容」の形式で入力します。', array() );
	$add_field( 'touch_schedule', '「ふれる」開催時間', 'textarea', $schedule_loop, '1行につき「時間｜内容」の形式で入力します。', array() );
	$add_field( 'kirei_schedule_note', '日時・開催場所：注記', 'textarea', 0, '', array() );
	$add_field( 'kirei_venue_guide_heading', '会場案内：見出し', 'text', 0, '', array() );

	$add_field( 'kirei_program_heading', '開催内容：見出し', 'text', 0, '', array() );
	$program_loop = $add_field(
		'kirei_program_rows',
		'開催内容一覧',
		'loop',
		0,
		'',
		array(
			'row_display' => 0,
			'row_label'   => '開催内容',
			'button_label'=> '開催内容を追加',
			'limit_min'   => '',
			'limit_max'   => '',
		)
	);
	$add_field( 'program_keyword', 'キーワード', 'text', $program_loop, '', array() );
	$add_field( 'program_lead', 'リード', 'text', $program_loop, '', array() );
	$add_field( 'program_title', '内容名', 'text', $program_loop, '', array() );
	$add_field( 'program_description', '説明', 'textarea', $program_loop, '', array() );
	$add_field(
		'program_image',
		'画像',
		'file',
		$program_loop,
		'',
		array(
			'file_type'    => 'image',
			'return_value' => 'url',
		)
	);
	$add_field( 'program_image_alt', '画像alt', 'text', $program_loop, '', array() );
	$add_field(
		'program_color',
		'アクセント色',
		'select',
		$program_loop,
		'',
		array(
			'choices'      => array(
				'rose'  => 'ピンク',
				'green' => 'グリーン',
				'blue'  => 'ブルー',
			),
			'force_single' => 1,
		)
	);
	$add_field( 'kirei_program_note', '開催内容：注記', 'textarea', 0, '', array() );

	$add_field( 'kirei_people_heading', '出演者：見出し', 'text', 0, '', array() );
	$people_loop = $add_field(
		'kirei_people_rows',
		'出演者一覧',
		'loop',
		0,
		'',
		array(
			'row_display' => 0,
			'row_label'   => '出演者',
			'button_label'=> '出演者を追加',
			'limit_min'   => '',
			'limit_max'   => '',
		)
	);
	$add_field(
		'person_program',
		'関連する開催内容',
		'select',
		$people_loop,
		'',
		array(
			'choices'      => array(
				'see'    => 'みる',
				'listen' => 'きく',
			),
			'force_single' => 1,
		)
	);
	$add_field( 'person_label', '区分', 'text', $people_loop, '例：アーティスト、トークゲスト、MC', array() );
	$add_field( 'person_role', '肩書き', 'text', $people_loop, '', array() );
	$add_field( 'person_name', '氏名', 'text', $people_loop, '「さん」はページ側で表示します。', array() );
	$add_field( 'person_profile', 'プロフィール', 'textarea', $people_loop, '', array() );
	$add_field(
		'person_image',
		'写真',
		'file',
		$people_loop,
		'',
		array(
			'file_type'    => 'image',
			'return_value' => 'url',
		)
	);
	$add_field( 'person_image_alt', '写真の説明', 'text', $people_loop, '', array() );
	$add_field( 'kirei_closing_message', 'クロージングメッセージ', 'text', 0, '', array() );

	return array(
		'post_title' => 'Kirei 2026',
		'post_name'  => 'kirei-2026',
		'cfs_fields' => $fields,
		'cfs_rules'  => array(
			'post_ids' => array(
				'operator' => '==',
				'values'   => array( (string) $page_id ),
			),
		),
		'cfs_extras' => array( 'order' => 0 ),
	);
}

function kirei2026_sync_schedule_values() {
	return array(
		'kirei_schedule_heading' => '日時・開催場所',
		'kirei_schedule_rows'    => array(
			array(
				'schedule_area'    => '横浜会場',
				'schedule_date'    => '12.3',
				'schedule_weekday' => '木',
				'schedule_time'    => '10:00〜12:00',
				'schedule_venue'   => 'パシフィコ横浜 マリンロビー',
				'floor_map_image'  => '',
				'floor_map_alt'    => '横浜会場のフロアマップ',
				'floor_map_caption'=> 'A：みる／B：きく／C：ふれる',
				'combine_see_listen'=> 'separate',
				'see_schedule'     => "10:15〜10:30｜素肌感を活かすメイク\n10:40〜10:55｜気分を彩るメイク\n11:05〜11:20｜素肌感を活かすメイク\n11:30〜11:45｜気分を彩るメイク",
				'listen_schedule'  => "10:15〜10:30｜これからはもっとワガママに\n10:40〜10:55｜私らしく輝くということ\n11:05〜11:20｜これからはもっとワガママに\n11:30〜11:45｜私らしく輝くということ",
				'touch_schedule'   => '10:00〜12:00｜ミキの化粧品の展示。ドゥース デュレシリーズは、ご希望の方にはタッチアップもいただけます。',
			),
			array(
				'schedule_area'    => '大阪会場',
				'schedule_date'    => '12.8',
				'schedule_weekday' => '火',
				'schedule_time'    => '10:00〜12:00',
				'schedule_venue'   => 'グランキューブ大阪 メインホワイエ',
				'floor_map_image'  => '',
				'floor_map_alt'    => '大阪会場のフロアマップ',
				'floor_map_caption'=> 'A・B：みる・きく共通ステージ／C：ふれる',
				'combine_see_listen'=> 'combined',
				'see_schedule'     => "10:15〜10:30｜素肌感を活かすメイク\n11:05〜11:20｜気分を彩るメイク",
				'listen_schedule'  => "10:40〜10:55｜これからはもっとワガママに\n11:30〜11:45｜私らしく輝くということ",
				'touch_schedule'   => '10:00〜12:00｜ミキの化粧品の展示。ドゥース デュレシリーズは、ご希望の方にはタッチアップもいただけます。',
			),
			array(
				'schedule_area'    => '福岡会場',
				'schedule_date'    => '12.15',
				'schedule_weekday' => '火',
				'schedule_time'    => '10:00〜12:00',
				'schedule_venue'   => '福岡サンパレス 大ホール',
				'floor_map_image'  => '',
				'floor_map_alt'    => '福岡会場のフロアマップ',
				'floor_map_caption'=> 'A・B：みる・きく共通ステージ／C：ふれる',
				'combine_see_listen'=> 'combined',
				'see_schedule'     => "10:15〜10:30｜素肌感を活かすメイク\n11:05〜11:20｜気分を彩るメイク",
				'listen_schedule'  => "10:40〜10:55｜これからはもっとワガママに\n11:30〜11:45｜私らしく輝くということ",
				'touch_schedule'   => '10:00〜12:00｜ミキの化粧品の展示。ドゥース デュレシリーズは、ご希望の方にはタッチアップもいただけます。',
			),
		),
		'kirei_schedule_note'    => '※掲載の画像・イベント内容・構成はイメージです。実際の内容とは異なる場合があり、予告なく変更となる場合がございます。',
		'kirei_venue_guide_heading' => '会場案内・タイムスケジュール',
	);
}

function kirei2026_sync_page_values() {
	$asset_base = get_stylesheet_directory_uri() . '/assets/image/kirei2026/';
	$home_host  = wp_parse_url( home_url( '/' ), PHP_URL_HOST );

	// 本番は管理CLI実行時も公開URLと同じHTTPSで画像URLを保存します。
	if ( 'www.mikiprune.co.jp' === $home_host ) {
		$asset_base = set_url_scheme( $asset_base, 'https' );
	}

	$schedule_values = kirei2026_sync_schedule_values();
	$map_files       = array( 'floor-map-yokohama.png', 'floor-map-osaka.png', 'floor-map-fukuoka.png' );

	foreach ( $schedule_values['kirei_schedule_rows'] as $index => &$schedule_row ) {
		if ( isset( $map_files[ $index ] ) ) {
			$schedule_row['floor_map_image'] = $asset_base . $map_files[ $index ];
		}
	}
	unset( $schedule_row );

	return array_merge(
		$schedule_values,
		array(
			'kirei_program_heading' => '開催内容',
			'kirei_program_rows'    => array(
				array(
					'program_keyword'     => 'みる',
					'program_lead'        => 'キレイはここからはじまる',
					'program_title'       => 'メイクアップショーステージ',
					'program_description' => 'さまざまなシチュエーションを想定したメイクデモンストレーション。化粧品の使い方のコツもお伝えします。',
					'program_image'       => $asset_base . 'program-see.jpg',
					'program_image_alt'   => 'メイクアップショーのイメージ',
					'program_color'       => 'rose',
				),
				array(
					'program_keyword'     => 'きく',
					'program_lead'        => 'キレイのヒントがここにある',
					'program_title'       => '美容トークショー「〜輝け、新しい私〜」',
					'program_description' => '美しさを育むヒントや、年齢を重ねることを前向きに楽しむための考え方などをお届けします。',
					'program_image'       => $asset_base . 'program-listen.jpg',
					'program_image_alt'   => '美容トークショーのイメージ',
					'program_color'       => 'green',
				),
				array(
					'program_keyword'     => 'ふれる',
					'program_lead'        => 'キレイを手に入れる',
					'program_title'       => 'タッチアップブース',
					'program_description' => 'スキンケアからベースメイクまで、ミキの化粧品を見て、触れて、お試しいただけます。',
					'program_image'       => $asset_base . 'program-touch.jpg',
					'program_image_alt'   => 'タッチアップブースのイメージ',
					'program_color'       => 'blue',
				),
			),
			'kirei_program_note'    => '※掲載の画像・イベント内容・構成はイメージです。実際の内容とは異なる場合があります。',
			'kirei_people_heading'  => '出演者',
			'kirei_people_rows'     => array(
				array(
					'person_program'   => 'see',
					'person_label'     => 'アーティスト',
					'person_role'      => 'ヘアメイクアップアーティスト',
					'person_name'      => '河野 祐樹',
					'person_profile'   => '「Beautyで人々を幸せにし、世の中に貢献する」を信念に、パリコレをはじめ国内外で活躍。技術指導や商品開発を通じて、美容の価値を次世代へ継承している。',
					'person_image'     => $asset_base . 'artist-kono.jpg',
					'person_image_alt' => 'ヘアメイクアップアーティスト 河野祐樹さん',
				),
				array(
					'person_program'   => 'listen',
					'person_label'     => 'トークゲスト',
					'person_role'      => '',
					'person_name'      => '板井 麻衣子',
					'person_profile'   => '2010年度ミス・ユニバース・ジャパンにてグランプリを受賞。その後ラジオのナビゲーターとして活躍の場を広げ、現在はJ-WAVEの番組を担当。モデル、MC、レポーター等多岐に渡って活躍中。',
					'person_image'     => $asset_base . 'guest-itai.jpg',
					'person_image_alt' => 'トークゲスト 板井麻衣子さん',
				),
				array(
					'person_program'   => 'listen',
					'person_label'     => 'MC',
					'person_role'      => '',
					'person_name'      => '横山 エリカ',
					'person_profile'   => '日本語、英語、ドイツ語を話すトリリンガル。ラジオパーソナリティ、MC、モデルなどで活躍中。',
					'person_image'     => $asset_base . 'mc-yokoyama.jpg',
					'person_image_alt' => 'MC 横山エリカさん',
				),
			),
			'kirei_closing_message' => 'キレイがきっと見つかる特別な時間（とき）',
		)
	);
}

$page  = get_page_by_path( 'kirei2026', OBJECT, 'page' );
$group = get_page_by_path( 'kirei-2026', OBJECT, 'cfs' );

if ( ! $apply ) {
	kirei2026_sync_report(
		array(
			'ok'           => true,
			'mode'         => 'probe',
			'page_exists'  => (bool) $page,
			'page_id'      => $page ? (int) $page->ID : 0,
			'group_exists' => (bool) $group,
			'group_id'     => $group ? (int) $group->ID : 0,
		)
	);
	exit( 0 );
}

if ( ! $page ) {
	$page_id = wp_insert_post(
		array(
			'post_title'  => 'Kirei 2026',
			'post_name'   => 'kirei2026',
			'post_type'   => 'page',
			'post_status' => 'publish',
			'post_content'=> '',
		),
		true
	);

	if ( is_wp_error( $page_id ) ) {
		fwrite( STDERR, $page_id->get_error_message() . PHP_EOL );
		exit( 1 );
	}
	$page = get_post( $page_id );
} else {
	wp_update_post(
		array(
			'ID'          => $page->ID,
			'post_title'  => 'Kirei 2026',
			'post_name'   => 'kirei2026',
			'post_status' => 'publish',
		)
	);
}

update_post_meta( $page->ID, '_wp_page_template', 'page-kirei2026.php' );

$field_schema = kirei2026_sync_field_schema( $page->ID );

if ( ! $group ) {
	CFS()->field_group->import(
		array(
			'import_code' => array( $field_schema ),
		)
	);
	$group = get_page_by_path( 'kirei-2026', OBJECT, 'cfs' );
}

if ( ! $group ) {
	fwrite( STDERR, "Failed to create the CFS field group.\n" );
	exit( 1 );
}

// 既存のフィールドグループも、今回の定義に同期します。
wp_update_post(
	array(
		'ID'         => $group->ID,
		'post_title' => $field_schema['post_title'],
		'post_name'  => $field_schema['post_name'],
	)
);
update_post_meta( $group->ID, 'cfs_fields', $field_schema['cfs_fields'] );
update_post_meta( $group->ID, 'cfs_rules', $field_schema['cfs_rules'] );
update_post_meta( $group->ID, 'cfs_extras', $field_schema['cfs_extras'] );

CFS()->field_group->cache = array();
CFS()->api->cache         = array();

$page_values          = kirei2026_sync_page_values();
$current_schedule_rows = (array) CFS()->get( 'kirei_schedule_rows', $page->ID );
$current_program_rows  = (array) CFS()->get( 'kirei_program_rows', $page->ID );
$current_people_rows   = (array) CFS()->get( 'kirei_people_rows', $page->ID );
$has_program_content   = false;

foreach ( $current_program_rows as $program_row ) {
	foreach ( array( 'program_keyword', 'program_lead', 'program_title', 'program_description', 'program_image' ) as $program_field ) {
		if ( ! empty( $program_row[ $program_field ] ) ) {
			$has_program_content = true;
			break 2;
		}
	}
}

if ( empty( $current_schedule_rows ) ) {
	$current_schedule_rows = $page_values['kirei_schedule_rows'];
} else {
	$default_schedule_by_area = array();
	foreach ( $page_values['kirei_schedule_rows'] as $default_schedule_row ) {
		$default_schedule_by_area[ $default_schedule_row['schedule_area'] ] = $default_schedule_row;
	}

	$new_schedule_fields = array(
		'floor_map_image',
		'floor_map_alt',
		'floor_map_caption',
		'combine_see_listen',
		'see_schedule',
		'listen_schedule',
		'touch_schedule',
	);

	foreach ( $current_schedule_rows as &$current_schedule_row ) {
		$area = isset( $current_schedule_row['schedule_area'] ) ? $current_schedule_row['schedule_area'] : '';
		if ( ! isset( $default_schedule_by_area[ $area ] ) ) {
			continue;
		}

		foreach ( $new_schedule_fields as $field_name ) {
			if ( empty( $current_schedule_row[ $field_name ] ) ) {
				$current_schedule_row[ $field_name ] = $default_schedule_by_area[ $area ][ $field_name ];
			}
		}
	}
	unset( $current_schedule_row );
}

$values_to_save = array(
	'kirei_schedule_rows' => $current_schedule_rows,
);

foreach ( array( 'kirei_schedule_heading', 'kirei_venue_guide_heading' ) as $field_name ) {
	if ( '' === trim( (string) CFS()->get( $field_name, $page->ID ) ) ) {
		$values_to_save[ $field_name ] = $page_values[ $field_name ];
	}
}

$current_schedule_note = trim( (string) CFS()->get( 'kirei_schedule_note', $page->ID ) );
if ( '' === $current_schedule_note || '※詳細は準備が整い次第、お知らせいたします。' === $current_schedule_note ) {
	$values_to_save['kirei_schedule_note'] = $page_values['kirei_schedule_note'];
}

if ( ! $has_program_content ) {
	$values_to_save['kirei_program_heading'] = $page_values['kirei_program_heading'];
	$values_to_save['kirei_program_rows']    = $page_values['kirei_program_rows'];
	$values_to_save['kirei_program_note']    = $page_values['kirei_program_note'];
}

if ( empty( $current_people_rows ) ) {
	$values_to_save['kirei_people_heading'] = $page_values['kirei_people_heading'];
	$values_to_save['kirei_people_rows']    = $page_values['kirei_people_rows'];
}

if ( '' === trim( (string) CFS()->get( 'kirei_closing_message', $page->ID ) ) ) {
	$values_to_save['kirei_closing_message'] = $page_values['kirei_closing_message'];
}

CFS()->save( $values_to_save, array( 'ID' => (int) $page->ID ) );

CFS()->field_group->cache = array();
CFS()->api->cache         = array();

$saved_fields = (array) get_post_meta( $group->ID, 'cfs_fields', true );
$saved_rows   = (array) CFS()->get( 'kirei_schedule_rows', $page->ID );
$saved_program_rows = (array) CFS()->get( 'kirei_program_rows', $page->ID );
$saved_people_rows  = (array) CFS()->get( 'kirei_people_rows', $page->ID );
$saved_closing = (string) CFS()->get( 'kirei_closing_message', $page->ID );
$template     = get_post_meta( $page->ID, '_wp_page_template', true );
$venue_details_complete = 3 === count( $saved_rows );

foreach ( $saved_rows as $saved_row ) {
	foreach ( array( 'floor_map_image', 'see_schedule', 'listen_schedule', 'touch_schedule' ) as $required_field ) {
		if ( empty( $saved_row[ $required_field ] ) ) {
			$venue_details_complete = false;
			break 2;
		}
	}
}

$ok = 'publish' === get_post_status( $page->ID )
	&& 'page-kirei2026.php' === $template
	&& 36 === count( $saved_fields )
	&& 3 === count( $saved_rows )
	&& 3 === count( $saved_program_rows )
	&& 3 === count( $saved_people_rows )
	&& $venue_details_complete
	&& '' !== trim( $saved_closing );

kirei2026_sync_report(
	array(
		'ok'                 => $ok,
		'mode'               => 'apply',
		'page_id'            => (int) $page->ID,
		'page_status'        => get_post_status( $page->ID ),
		'page_slug'          => get_post_field( 'post_name', $page->ID ),
		'template'           => $template,
		'group_id'           => (int) $group->ID,
		'field_count'        => count( $saved_fields ),
		'schedule_row_count' => count( $saved_rows ),
		'program_row_count'  => count( $saved_program_rows ),
		'people_row_count'   => count( $saved_people_rows ),
		'venue_details_complete' => $venue_details_complete,
		'closing_registered' => '' !== trim( $saved_closing ),
	)
);

exit( $ok ? 0 : 1 );
