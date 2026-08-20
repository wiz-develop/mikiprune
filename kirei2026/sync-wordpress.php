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
	$add_field( 'access_url', 'アクセスURL', 'text', $schedule_loop, '空欄の場合は非表示', array() );
	$add_field( 'floor_map_url', 'フロアマップURL', 'text', $schedule_loop, '空欄の場合は非表示', array() );
	$add_field( 'timetable_url', 'タイムスケジュールURL', 'text', $schedule_loop, '空欄の場合は非表示', array() );
	$add_field( 'kirei_schedule_note', '日時・開催場所：注記', 'textarea', 0, '', array() );

	$add_field( 'kirei_program_heading', '開催内容：見出し', 'text', 0, '', array() );
	$program_loop = $add_field(
		'kirei_program_rows',
		'開催内容一覧',
		'loop',
		0,
		'',
		array(
			'row_display' => 0,
			'row_label'   => '{program_keyword:開催内容}',
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

	$add_field( 'kirei_guest_heading', '出演者プロフィール：見出し', 'text', 0, '', array() );
	$add_field( 'kirei_guest_name', '氏名', 'text', 0, '', array() );
	$add_field( 'kirei_guest_role', '肩書き', 'text', 0, '', array() );
	$add_field( 'kirei_guest_profile', 'プロフィール', 'wysiwyg', 0, '', array() );
	$add_field(
		'kirei_guest_image',
		'写真',
		'file',
		0,
		'',
		array(
			'file_type'    => 'image',
			'return_value' => 'url',
		)
	);
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
				'access_url'       => '',
				'floor_map_url'    => '',
				'timetable_url'    => '',
			),
			array(
				'schedule_area'    => '大阪会場',
				'schedule_date'    => '12.8',
				'schedule_weekday' => '火',
				'schedule_time'    => '10:00〜12:00',
				'schedule_venue'   => 'グランキューブ大阪 メインホワイエ',
				'access_url'       => '',
				'floor_map_url'    => '',
				'timetable_url'    => '',
			),
			array(
				'schedule_area'    => '福岡会場',
				'schedule_date'    => '12.15',
				'schedule_weekday' => '火',
				'schedule_time'    => '10:00〜12:00',
				'schedule_venue'   => '福岡サンパレス 大ホール',
				'access_url'       => '',
				'floor_map_url'    => '',
				'timetable_url'    => '',
			),
		),
		'kirei_schedule_note'    => '※詳細は準備が整い次第、お知らせいたします。',
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

if ( ! $group ) {
	CFS()->field_group->import(
		array(
			'import_code' => array( kirei2026_sync_field_schema( $page->ID ) ),
		)
	);
	$group = get_page_by_path( 'kirei-2026', OBJECT, 'cfs' );
}

if ( ! $group ) {
	fwrite( STDERR, "Failed to create the CFS field group.\n" );
	exit( 1 );
}

CFS()->field_group->cache = array();
CFS()->api->cache         = array();

$current_rows = (array) CFS()->get( 'kirei_schedule_rows', $page->ID );
if ( empty( $current_rows ) ) {
	CFS()->save( kirei2026_sync_schedule_values(), array( 'ID' => (int) $page->ID ) );
}

CFS()->field_group->cache = array();
CFS()->api->cache         = array();

$saved_fields = (array) get_post_meta( $group->ID, 'cfs_fields', true );
$saved_rows   = (array) CFS()->get( 'kirei_schedule_rows', $page->ID );
$template     = get_post_meta( $page->ID, '_wp_page_template', true );

$ok = 'publish' === get_post_status( $page->ID )
	&& 'page-kirei2026.php' === $template
	&& 27 === count( $saved_fields )
	&& 3 === count( $saved_rows );

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
	)
);

exit( $ok ? 0 : 1 );
