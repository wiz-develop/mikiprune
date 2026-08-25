jQuery(function($){ 
  /*-------------------------------------------*/
  /* jsでサイトのURL・テーマのパスを使えるようにする
  /*-------------------------------------------*/
  var wp_temp_uri = tmp_path.temp_uri; //テンプレートURL
  var wp_home_url = tmp_path.home_url; //サイトURL

  /*-------------------------------------------*/
  /* スライドショー
  /*-------------------------------------------*/
  // ファーストビューに動画があるとき
  window.addEventListener('load', function(){
    var videoElem = $('#firstview_video').get(0);
    if (videoElem) {
      var videoTime = Math.floor(videoElem.duration * 1000) + 1000;

      $("#firstview_slide_video").slick({
        autoplay: true, // 自動再生ON
        fade: true, // フェードON
        arrows: false, // 矢印OFF
        speed: 2000, // スライド、フェードアニメーションの速度2000ミリ秒
        autoplaySpeed: videoTime, // 自動再生速度4000ミリ秒
        pauseOnFocus: false, // フォーカスで一時停止OFF
        pauseOnHover: false, // マウスホバーで一時停止OFF
        autoplaySpeed: 4000,
        })
      .on('afterChange', function(event, slick, currentSlide, nextSlide) {
        switch (currentSlide) {
          case 1:
            $(this).slick('slickSetOption', 'autoplaySpeed', videoTime);
            videoElem.pause();
            videoElem.currentTime = 0;
            setTimeout(function () {
              videoElem.play();
            }, 1000);
            break;
          default:
            $(this).slick('slickSetOption', 'autoplaySpeed', 4000);
            videoElem.pause();
            videoElem.currentTime = 0;
            break;
        }
      });
    }
  });

  // ファーストビューが画像のみのとき
  $("#firstview_slide").slick({
    autoplay: true,
    fade: true,
    arrows: false,
    speed: 2000,
    autoplaySpeed: 4000,
    pauseOnFocus: false,
    pauseOnHover: false,
  });

  $(".slide-office")
    // 最初のスライドに"add-animation"のclassを付ける(data-slick-index="0"が最初のスライドを指す)
    .on("init", function () {
      $('.slick-slide[data-slick-index="0"]').addClass("add-animation");
    })
    // 通常のオプション
    .slick({
      autoplay: true, // 自動再生ON
      fade: true, // フェードON
      arrows: false, // 矢印OFF
      speed: 2000, // スライド、フェードアニメーションの速度2000ミリ秒
      autoplaySpeed: 4000, // 自動再生速度4000ミリ秒
      pauseOnFocus: false, // フォーカスで一時停止OFF
      pauseOnHover: false, // マウスホバーで一時停止OFF
    })
    .on({
      // スライドが移動する前に発生するイベント
      beforeChange: function (event, slick, currentSlide, nextSlide) {
        // 表示されているスライドに"add-animation"のclassをつける
        $(".slick-slide", this).eq(nextSlide).addClass("add-animation");
        // あとで"add-animation"のclassを消すための"remove-animation"classを付ける
        $(".slick-slide", this).eq(currentSlide).addClass("remove-animation");
      },
      // スライドが移動した後に発生するイベント
      afterChange: function () {
        // 表示していないスライドはアニメーションのclassを外す
        $(".remove-animation", this).removeClass(
          "remove-animation add-animation"
        );
      },
    });
  slideAlignHeight('.slide-office .slick-slide');

  $('.pickup_carousel').slick({
    autoplay: true,         //自動再生
    autoplaySpeed: 2000,    //自動再生のスピード
    speed: 800,             //スライドするスピード
    dots: true,             //スライドしたのドット
    arrows: true,           //左右の矢印
    infinite: true,         //スライドのループ
    pauseOnHover: false,    //ホバーしたときにスライドを一時停止しない
    slidesToShow: 4,
    slidesToScroll: 1,
    prevArrow: '<button type="button" class="slick-prev"><img src="'+wp_temp_uri+'/assets/image/common/slick-prev.png"></button>',
    nextArrow: '<button type="button" class="slick-next"><img src="'+wp_temp_uri+'/assets/image/common/slick-next.png"></button>',
    responsive: [{
      breakpoint: 992, // ブレイクポイントを指定
       settings: {
        slidesToShow: 3,
       },
      },
     {
      breakpoint: 576,
       settings: {
        slidesToShow: 2,
      },
     },
    ]
  });
  slideAlignHeight('.pickup_carousel .slide_item'); // スライドの高さを揃える

  $('.product-slider').slick({
    autoplay: true,         //自動再生
    autoplaySpeed: 2000,    //自動再生のスピード
    speed: 400,             //スライドするスピード
    dots: true,             //スライドしたのドット
    arrows: true,           //左右の矢印
    infinite: true,         //スライドのループ
    pauseOnHover: false,    //ホバーしたときにスライドを一時停止しない
    slidesToShow: 4,
    slidesToScroll: 1,
    prevArrow: '<button type="button" class="slick-prev"><img src="'+wp_temp_uri+'/assets/image/common/slick-prev.png"></button>',
    nextArrow: '<button type="button" class="slick-next"><img src="'+wp_temp_uri+'/assets/image/common/slick-next.png"></button>',
    responsive: [{
      breakpoint: 992, // ブレイクポイントを指定
       settings: {
        slidesToShow: 3,
       },
      },
     {
      breakpoint: 576,
       settings: {
        slidesToShow: 2,
      },
     },
    ]
  });

  $('.product-photo__slider').slick({
    slidesToShow: 1,
    slidesToScroll: 1,
    autoplay: true,
    arrows: true,
    fade: true,
    infinite: true,
    dots: true,
    prevArrow: '<button type="button" class="slick-prev"><img src="'+wp_temp_uri+'/assets/image/common/slick-prev.png"></button>',
    nextArrow: '<button type="button" class="slick-next"><img src="'+wp_temp_uri+'/assets/image/common/slick-next.png"></button>',
    // asNavFor: '.product-photo__th',
  });
  $('.product-photo__th').slick({
    // slidesToShow: 4,
    // slidesToScroll: 1,
    // asNavFor: '.product-photo__slider',
    // dots: true,
    // focusOnSelect: true,
    // arrows: true,
    // infinite: true,
    // prevArrow: '<button type="button" class="slick-prev"><img src="'+wp_temp_uri+'/assets/image/common/slick-prev.png"></button>',
    // nextArrow: '<button type="button" class="slick-next"><img src="'+wp_temp_uri+'/assets/image/common/slick-next.png"></button>',
  });
  // $('.product-photo__slider').on('beforeChange', function(event, slick, currentSlide, nextSlide){
  //     if ($('.product-photo__th .th-img').length <= 4) {
  //         $('.product-photo__th').slick('slickSetOption', {
  //           'centerMode': true, 
  //           'centerPadding': 0, 
  //         }, true);
  //     }
  // });

  // スライドの高さを揃える
  function slideAlignHeight($class) {
    window.addEventListener('load', function() {
      var maxSliderHeight = 0;
      $($class).each(function(idx, elem) {
        var sliderHeight = $(elem).height();
        if(maxSliderHeight < sliderHeight) {
          maxSliderHeight = sliderHeight;
        }
      });
      $($class).height(maxSliderHeight);
    });
  }

  /*-------------------------------------------*/
  /* ファーストビューの動画ボリューム
  /*-------------------------------------------*/
  var slider_volume = document.getElementById("volume");
  if ($('#volume').length > 0) {
    var firstview_video = document.getElementById("firstview_video");
    window.addEventListener('DOMContentLoaded', function(){
      // ボリュームの初期設定
      firstview_video.volume = slider_volume.value;
      // 音量調整スライダーを操作したとき
      slider_volume.addEventListener("input", (e) => {
        firstview_video.muted = false;
        firstview_video.volume = slider_volume.value;
      });
    });
  }

  /*-------------------------------------------*/
  /* スクロールしたらクラスを追加
  /*-------------------------------------------*/
  if (window.matchMedia('(min-width:768px)').matches) {
    $('.nav-product').addClass('open');
    $('.nav-product__btn').addClass('open');
    $('.nav-product__btn').next('.ac-child-left').addClass('open');
  }

  $(window).scroll(function () {
    var scrollAmount = $(window).scrollTop();
    if (scrollAmount > 0) {
      $('body').addClass('scrolled');
      $('.nav-product').removeClass('open');
      $('.nav-product__btn').removeClass('open');
      $('.nav-product__btn').next('.ac-child-left').removeClass('open');
    } else {
      $('body').removeClass('scrolled');
    }

    ScrollAnime();
  });

  /*-------------------------------------------*/
  /* スムーススクロール
  /*-------------------------------------------*/
  var HeaderHeight = $('.site-header').outerHeight();
  var speed = 100;
	$('a[href^="#"]').on('click', function() {
    $(this).off('click');
		var href= $(this).attr("href");
		var target = $(href == "#" || href == "" ? 'html' : href);
		var position = target.offset().top - HeaderHeight - 30;
		$('body,html').animate({scrollTop:position}, speed, 'swing');
		return false;
  });

  $(window).on('load',function(event){
    //URLのハッシュ値を取得
    var urlHash = location.hash;
    //ハッシュ値があればページ内スクロール
    if(urlHash) {
      if ($('#product_list #pills-tabContent').find(urlHash).length > 0) {
        // ボタン
        $('#product_list .tab-link').removeClass('active');
        var ariaLabelledby = '#' + $(urlHash).attr('aria-labelledby');
        $(ariaLabelledby).addClass('active');

        // タブ
        $('#product_list .tab-pane').removeClass('active show');
        $(urlHash).addClass('active show');
      } else {
        $('html,body').stop().scrollTop(0);
        hashposi = $(urlHash).offset().top - HeaderHeight - 30;
        setTimeout(function () {
          //ロード時の処理を待ち、時間差でスクロール実行
          $('body,html').animate({scrollTop:hashposi}, speed, 'swing');
        }, 100);        
      }
    }
  });

  /*-------------------------------------------*/
  /* パララックス
  /*-------------------------------------------*/
  // トップページコンセプト
  var target1 = $("#concept-parallax");
  if (document.getElementById('concept-parallax')) {
    var targetPosOT1 = target1.offset().top;
    var targetFactor = 0.5;
    var windowH = $(window).height();
    var scrollYStart1 = targetPosOT1 - windowH;
    $(window).on('scroll', function () {
        var scrollY = $(this).scrollTop();
        if (scrollY > scrollYStart1) {
            target1.css('background-position-y', (scrollY - targetPosOT1) * targetFactor + 'px');
        } 
    });
  }

  var target2 = $("#contact-parallax");
  if (document.getElementById('contact-parallax')) {
  var targetPosOT2 = target2.offset().top;
  var scrollYStart2 = targetPosOT2 - windowH;
  $(window).on('scroll', function () {
      var scrollY = $(this).scrollTop();
      if (scrollY > scrollYStart2) {
          target2.css('background-position-y', (scrollY - targetPosOT2) * targetFactor + 'px');
      } 
  });
}

  /*-------------------------------------------*/
  /* アニメーション
  /*-------------------------------------------*/
  // var wHeight = $(window).height();
  // $(window).scroll(function () {
  //   var scrollAmount = $(window).scrollTop();
  //   $('.anime').each(function () {
  //     var targetPosition = $(this).offset().top;
  //     if(scrollAmount > targetPosition - wHeight + 60) {
  //         $(this).addClass('anime_active');
  //     }
  //   });
  // });

  $('.anime').addClass('anime_active');

  /*-------------------------------------------*/
  /* フォントサイズを変更
  /*-------------------------------------------*/
  var selectFontSize = $('.change-font-size');
  var fontsize = '';

  selectFontSize.on('click', function() {
    fontsize = selectFontSize.val();
    changefontsize(fontsize);
  });

	function changefontsize(fontsize) {
		if (fontsize == 'normal') {
			$('html').css({'cssText':'font-size:1.0em !important;'});
      selectFontSize.val("big");
		} else if (fontsize == 'big') {
			$('html').css({'cssText':'font-size:1.2em !important;'});
      selectFontSize.val("normal");
		}
    sessionStorage.setItem("fontsize",fontsize);
	}

  if (sessionStorage.getItem("fontsize")){
    fontsize = sessionStorage.getItem("fontsize");
		changefontsize(fontsize);
	}

  /*-------------------------------------------*/
  /* ポップアップ
  /*-------------------------------------------*/
  // デフォルト
  $(document).on('click','.modal_trigger', function(){
    var modal_box = $(this).next('.modal_box');
    modal_box.fadeIn(); // モーダルを表示する
    $('body').addClass('overflow-hidden');
    if (modal_box.find('video').get(0)) {
      modal_box.find('video').attr('controlslist', 'nodownload');
      modal_box.find('video').get(0).play();
      modal_box.find('video').addClass('playing');
    }
  });

	$('video').on("contextmenu",function(){
		return false;
	});

  // ポップアップを閉じる
  $(document).on('click','.modal_close , .modal_bg', function(){
    $('.modal_box').fadeOut(); // モーダルを非表示にする
    $('body').removeClass('overflow-hidden');
    $('.playing').get(0).pause();
    $('.playing').removeClass('playing');
  });

  // メニュー用
  $(document).on('click','.sitemap_trigger', function(){
    $('#sitemap_modal').fadeIn();
    $('body').addClass('overflow-hidden');
  });

  // 「探す」用
  $(document).on('click','.menu_trigger', function(){
    $('#menu_modal').fadeIn();
    $('body').addClass('overflow-hidden');
  });

  $(document).on('click','.menu_reset', function(){
    $('input[name="s"]').val("");
  });

  const searchParams = new URLSearchParams(window.location.search);
  if (searchParams.has('play')) {
    var play = '#'+searchParams.get('play');
    var modal_box = $(play).next('.modal_box');
    modal_box.fadeIn();
    modal_box.find('video').addClass('playing');
    $('body').addClass('overflow-hidden');
  }

  /*-------------------------------------------*/
  /* アコーディオン
  /*-------------------------------------------*/
  // 上から下へ表示
  $('.ac-parent').on('click', function() {
    $(this).toggleClass('open');
    $(this).next('.ac-child').slideToggle();
  });

  // 下から上へ表示
  // PCお問い合わせ
  var showContactPopup = "showContactPopup";
  if (sessionStorage.getItem(showContactPopup) != "close") {
      $('.contact-modal .ac-parent-bottom, .footer_hamburger .ac-parent-bottom').addClass('open');
      $('.contact-modal .ac-child-bottom, .contact-box-sp.ac-child-bottom').css('display', 'block');
  } else {
      $('.contact-modal .ac-parent-bottom, .footer_hamburger .ac-parent-bottom').removeClass('open');
      $('.contact-modal .ac-child-bottom, .contact-box-sp.ac-child-bottom').css('display', 'none');
  }

  $('#contact-page-link').on('click', function() {
    sessionStorage.setItem(showContactPopup, "close");
  });

  $('.ac-parent-bottom').on('click', function() {
    childBtnToggle(this);
  });

  $('#contact-box-sp-menu').on('click', function() {
    childBtnToggle('.footer_hamburger .ac-parent-bottom');
  });

  function childBtnToggle(parentBtn) {
		$(parentBtn).toggleClass('open');
    $(parentBtn).prev('.ac-child-bottom').slideToggle();
    $(parentBtn).children('.ac-child-bottom').slideToggle();
    if ($(parentBtn).hasClass('open')) {
      sessionStorage.setItem(showContactPopup, "open");
    } else {
      sessionStorage.setItem(showContactPopup, "close");
    }
  }

  // よくあるお問い合わせ
  $('.qa-sec-title').on('click', function() {
		$(this).next('.qa-sec').slideToggle();
	});

  // 右から左へ
  // 商品ページ内メニュー
  $('.ac-parent-left').on('click', function() {
    $(this).parent().toggleClass('open');
    $(this).toggleClass('open');
    $(this).next('.ac-child-left').toggleClass('open');
  });

  /*-------------------------------------------*/
  /* デザインレイアウト
  /*-------------------------------------------*/
  $('#title_type_arch').arctext({
    radius : 400,
  });

  /*-------------------------------------------*/
  /* スマホのみ、telリンククリックで発信する
  /*-------------------------------------------*/
	var ua = navigator.userAgent.toLowerCase();
	var isMobile = /iphone/.test(ua)||/android(.+)?mobile/.test(ua);

	if (!isMobile) {
			jQuery('a[href^="tel:"]').on('click', function(e) {
			e.preventDefault();
		});
	}

  /*-------------------------------------------*/
  /* 商品一覧
  /*-------------------------------------------*/
  // タブ切り替え時にハッシュを追加
  $('#pills-tab .tab-link').on('click', function() {
    var id = $(this).attr('id');
    var bsTarget = $(this).data('bs-target');
    location.hash = bsTarget;
  });

  // if ($('#pills-tab .tab-link') && location.hash) {
  //   var urlHash = location.hash;

  //   // ボタン
  //   $('.tab-link').removeClass('active');
  //   var ariaLabelledby = '#' + $(urlHash).attr('aria-labelledby');
  //   $(ariaLabelledby).addClass('active');

  //   // タブ
  //   $('.tab-pane').removeClass('active show');
  //   $(urlHash).addClass('active show');
  // }

  /*-------------------------------------------*/
  /* ブログ・新着情報
  /*-------------------------------------------*/
  // カテゴリー一覧を開閉する
  $('#toggle-cat-btn').on('click', function() {
    $('.toggle-cat').toggleClass('d-none');
    $(this).toggleClass('open');
    if (!$('.toggle-cat').hasClass('d-none')) {
      $(this).children('.toggle-cat-btn-text').text('カテゴリーを閉じる');
    } else {
      $(this).children('.toggle-cat-btn-text').text('カテゴリーを全て見る');
    }
  });

  // もっと見るボタン
  // 現在表示されている数
  var blog_now_post = $('#js_late_posts .slide_item').length + $('.latest_column .slide_item').length;
  var recommended_now_post = $('#js_recommended_posts .slide_item').length;
  var good_now_post = $('#js_good_posts .slide_item').length;

  // 一度に取得する数
  var get_post_num = 6;

  $('.more_disp').on('click', function() {
    var button = $(this);
    button.css('pointer-events','none');

    var post_cat =  $(this).children('button').data('post');
    var now_post_num = 0;
    var ajax_url = wp_temp_uri+'/api/blog-readmore.php';

    if (post_cat == "blog") {
      now_post_num = blog_now_post;
    } else if (post_cat == "recommended") {
      now_post_num = recommended_now_post;
    } else if (post_cat == "good") {
      now_post_num = good_now_post;
    }

    $.ajax({
      type: 'POST',
      url: ajax_url,
      data: {
          'now_post_num': now_post_num,
          'get_post_num': get_post_num,
          'post_cat': post_cat,
      },
      dataType: 'html',
    })
    .done(function(data){
      if (!$.trim(data)) {
        button.hide();
        return;
      }
      now_post_num = now_post_num + get_post_num;
      if (post_cat == "blog") {
        blog_now_post = now_post_num;
      } else if (post_cat == "recommended") {
        recommended_now_post = now_post_num;
      } else if (post_cat == "good") {
        good_now_post = now_post_num;
      }
      button.before(data);
      button.css('pointer-events','auto');
    })
    .fail(function(){ // ajax通信成失敗の処理
      console.log('エラーが発生しました');
      button.css('pointer-events','auto');
    })
    return false;
  });

  // いいねボタン
  var blog_id = $('.good-btn-js').data('post');
  if (blog_id) {
    var blog_session_id = 'blog_'+blog_id,
        blog_session_exist = localStorage.getItem(blog_session_id);
    if (blog_session_exist) {
        $('.heart-icon').attr('src', wp_temp_uri+'/assets/image/blog/good_1_on.png');
        $('.heart-icon').removeClass('heart-icon-hover');
    }
  }

  $('.good-btn-js').on('click', function() {
      var button = $(this);
      button.css('pointer-events','none');
      blog_session_exist = localStorage.getItem(blog_session_id);
      if (blog_session_exist) {
          $('.good-title').text('投票済み');
      } else {
          var ajax_url = wp_temp_uri+'/api/blog-good-add.php';

          $.ajax({
          type: 'POST',
          url: ajax_url,
          data: {
              'blog_id': blog_id,
          },
          dataType: 'text',
          })
          .done(function(data){
              if (data > 0) {
                  localStorage.setItem(blog_session_id, true);
                  $('.heart-icon').attr('src', wp_temp_uri+'/assets/image/blog/good_1_on.png');
                  $('.heart-icon').removeClass('heart-icon-hover');
                  $('.good-title').text('いいね！');
                  $('.good-count').text(data);
                  console.log(data);
              } else {
                  button.children('.good-title').text('いいね！が投票できませんでした。再度お試しください。');
              }
          })
          .fail(function(){ // ajax通信成失敗の処理
              button.children('.good-title').text('いいね！が投票できませんでした。再度お試しください。');
          })
          button.css('pointer-events','auto');
      }
  });

  /*-------------------------------------------*/
  /* フリーワード検索 全角空白を半角にする 
  /*-------------------------------------------*/
	var searchWordInput = document.getElementById('search_word');
	if(searchWordInput) {
		searchWordInput.addEventListener('blur', () => { 
			var searchWordInputValue = searchWordInput.value.replace(/　/g," ");
			searchWordInput.value = searchWordInputValue;
		}, false);
	}

  /*-------------------------------------------*/
  /* アーカイブ ページネーション
  /*-------------------------------------------*/
    if ($('.pnavi')) {
      $("a.page-numbers").each( function() {
          var pageNumbers = $(this).attr('href');
          if (pageNumbers == '') {
            $(this).attr('href', location.pathname);
          }
      });
    }

  /*-------------------------------------------*/
  /* ヘッダー
  /*-------------------------------------------*/
  var beforeTarget = 0;

  function ScrollAnime() {
      var scroll = $(window).scrollTop();
      var beforePos = scroll - beforeTarget;
      if(scroll == beforeTarget) {
        $('.js-site-header').addClass('js-DownMove');
        $('.js-site-header').removeClass('js-UpMove');
      } else if(HeaderHeight > scroll || 0 > beforePos){
        //ヘッダーが上から出現する
        $('.js-site-header').removeClass('js-UpMove');
        $('.js-site-header').addClass('js-DownMove');
      } else {
        //ヘッダーが上に消える
        $('.js-site-header').removeClass('js-DownMove');
        $('.js-site-header').addClass('js-UpMove');
      }
      beforeTarget = scroll;//現在のスクロール値を比較用のbeforePosに格納
  }

  /*-------------------------------------------*/
  /* 画像を右クリックできないようにする
  /*-------------------------------------------*/
  $("img").bind('contextmenu', function(e) {
      return false;
  });

  /*-------------------------------------------*/
  /* 動画再生ボタン
  /*-------------------------------------------*/
  // const video = document.querySelector('#video');
  // const video_btn = document.querySelector('#video-btn');
  // let is_playing = false;

  // video_btn.addEventListener('click', () => {
  //   if (!is_playing) {
  //     video.play();
  //     is_playing = true;
  //   } else {
  //     video.pause();
  //     is_playing = false;
  //   }
  // });


}); // このカッコの前にソースを書いてください。jQueryが効かなくなります

/*-------------------------------------------*/
/*  未整理のjs
/*-------------------------------------------*/
// $(function () {
  // $(function() {
  //   var height=$(".site-header").height();
  //   $("body").css("margin-top", height + 10);//10pxだけ余裕をもたせる
  // });

  // $('.search_button button').click(function(){
  //   searchButtonClick();
  // });
  // $('.search-area .search_button button').click(function(){
  //   $('.search-areah').fadeOut('slow');
  // });

  // $(function() {
  //   let tabs = $(".tab"); // tabのクラスを全て取得し、変数tabsに配列で定義
  //   $(".tab").on("click", function() { // tabをクリックしたらイベント発火
  //     $(".active").removeClass("active"); // activeクラスを消す
  //     $(this).addClass("active"); // クリックした箇所にactiveクラスを追加
  //     const index = tabs.index(this); // クリックした箇所がタブの何番目か判定し、定数indexとして定義
  //     $(".content").removeClass("show").eq(index).addClass("show"); // showクラスを消して、contentクラスのindex番目にshowクラスを追加
  //   })
  // })
// });
