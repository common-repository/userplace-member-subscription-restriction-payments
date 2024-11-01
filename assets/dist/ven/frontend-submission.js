"use strict";
var $ = jQuery;
$(document).ready(function () {
  /*====================================
	=            Push Menu           =
	====================================*/
  // ON DOM READY
  $(function () {
    $(".toggle-nav").click(function () {
      // Calling a function in case you want to expand upon this.
      toggleNav();
    });
    $(document).on("click", function (e) {
      var navBtnAndWrapper = $(".up-userplace-leftside-nav, .toggle-nav");

      if ($(window).width() < 800) {
        if (
          !navBtnAndWrapper.is(e.target) &&
          navBtnAndWrapper.has(e.target).length === 0
        ) {
          $(".up-userplace-frontend--section").addClass("hide-nav");
        }
      }
    });

    if ($(window).width() < 768) {
      $(".up-userplace-frontend--section").addClass("hide-nav");
    }
  });

  // CUSTOM FUNCTIONS
  function toggleNav() {
    if ($(".up-userplace-frontend--section").hasClass("hide-nav")) {
      // Do things on Nav Close
      $(".up-userplace-frontend--section").removeClass("hide-nav");
    } else {
      // Do things on Nav Open
      $(".up-userplace-frontend--section").addClass("hide-nav");
    }
  }

  $(window).on("resize", function (e) {
    var clearResize;

    clearTimeout(clearResize);
    clearResize = setTimeout(function () {
      if ($(window).width() < 768) {
        $(".up-userplace-frontend--section").addClass("hide-nav");
      } else {
        $(".up-userplace-frontend--section").removeClass("hide-nav");
      }
    }, 50);
  });

  /* ==================================================================
                    PRICING PLAN SLIDER
================================================================== */
  if ($(".userplaceMultiplePaymentPackage").length > 0) {
    $(".userplaceMultiplePaymentPackage").owlCarousel({
      items: 3,
      autoplay: false,
      margin: 10,
      nav: true,
      navText: [
        '<i class="ion-chevron-left"></i>',
        '<i class="ion-chevron-right"></i>',
      ],
      responsive: {
        0: {
          items: 1,
          autoplay: false,
        },
        600: {
          items: 2,
          autoplay: false,
        },
        1000: {
          items: 3,
          loop: false,
          autoplay: false,
        },
      },
    });
  }

  /* ==================================================================
                      Header DROPDOWN
  ================================================================== */
  var headerDropDownBtns = $("header .up-dropdown--btn");
  headerDropDownBtns.on("click", function (e) {
    //e.preventDefault();
    if (
      $(this)
        .children(".up-userplace--dropdown")
        .hasClass("animate-dropdown-menu")
    ) {
      $(this)
        .children(".up-userplace--dropdown")
        .removeClass("animate-dropdown-menu");
    } else {
      headerDropDownBtns
        .children(".up-userplace--dropdown")
        .removeClass("animate-dropdown-menu");
      $(this)
        .children(".up-userplace--dropdown")
        .addClass("animate-dropdown-menu");
    }
  });

  $(document).on("click", function (e) {
    if (
      !headerDropDownBtns.is(e.target) &&
      headerDropDownBtns.has(e.target).length === 0
    ) {
      headerDropDownBtns.children("ul").removeClass("animate-dropdown-menu");
    }
  });

  /* ==================================================================
                      DROPDOWN
  ================================================================== */
  var dropDownBtns = $(".up-dropdown-toggle-btn");

  dropDownBtns.on("click", function (e) {
    e.preventDefault();

    if (
      $(this)
        .children(".up-userplace-option-dropdown")
        .hasClass("open-settings")
    ) {
      $(this)
        .children(".up-userplace-option-dropdown")
        .removeClass("open-settings")
        .hide();
    } else {
      dropDownBtns
        .children(".up-userplace-option-dropdown")
        .removeClass("open-settings")
        .hide();
      $(this)
        .children(".up-userplace-option-dropdown")
        .addClass("open-settings")
        .show();
    }
  });

  $(document).on("click", function (e) {
    if (!dropDownBtns.is(e.target) && dropDownBtns.has(e.target).length === 0) {
      dropDownBtns.children("ul").removeClass("open-settings").hide();
    }
  });

  $(".up-msg-del-btn").on("click", function (e) {
    e.preventDefault();
    var thisMsg = $(this).parents(".up-userplace-single-msg");
    swal(
      {
        title: "Are you sure?",
        text: "You will not be able to recover this message!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#ff6060",
        confirmButtonText: "Delete",
        closeOnConfirm: false,
        html: false,
      },
      function (isConfirm) {
        swal("Deleted!", "This message has been deleted.", "success");
        if (isConfirm) {
          thisMsg.remove();
        }
      }
    );
  });

  /* ==================================================================
                      Del Notification
  ================================================================== */
  $(".up-userplace-notification-close-btn").on("click", function (e) {
    e.preventDefault();
    /* Act on the event */

    $(this).parent().remove();
  });

  /* ==================================================================
                      Act Like Disabled btn for a tag
  ================================================================== */

  $("a").on("click", function (e) {
    if ($(this).hasClass("disabled")) {
      e.preventDefault();
    }
  });

  /*******************************/
  /* Sticky Header */

  /*******************************/

  $(window).scroll(function () {
    if ($(this).scrollTop() > 36) {
      $("body").addClass("sticky-header");
    } else {
      $("body").removeClass("sticky-header");
    }
  });

  /*******************************/
  /* Active Side bar menu class */
  /*******************************/
  // window.addEventListener('load', function() {
  //   var urlPath = window.location.href;
  //   var getMenuItem = document.querySelectorAll(
  //     'ul.up-userplace-nav-menu li a span'
  //   );

  //   for (var i = 0; i < getMenuItem.length; i++) {
  //     var currentMenuItem = getMenuItem[i].parentNode;
  //     var currentMenuPath = currentMenuItem.getAttribute('href');

  //     if (currentMenuPath.slice(-1) === '/') {
  //       if (urlPath == currentMenuPath) {
  //         currentMenuItem.setAttribute('class', 'active-page');
  //       }
  //     } else if (urlPath == `${currentMenuPath}/`) {
  //       currentMenuItem.setAttribute('class', 'active-page');
  //     }
  //   }
  // });

  /*******************************/
  /* NiceScroll for widget */
  /*******************************/
  $(window).load(function () {
    $(
      ".up-userplace-widget-body, .rqInvoiceTable .rqUserplaceTableBody"
    ).niceScroll({
      cursorwidth: 4,
      cursorborder: "none",
      cursorcolor: "#2d3446",
      cursorborderradius: 1,
      background: "transparent",
      hidecursordelay: 0,
      emulatetouch: true,
      cursordragontouch: true,
    });
  });
});
