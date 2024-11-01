"use strict";
var $ = jQuery;
$(document).ready(function () {
  $(".card-list").on("click", ".userplace-card", function (e) {
    console.log(e);
    e.preventDefault();
    $(this).html(
      '<div class="sk-circle sk-circle-red"><div class="sk-circle1 sk-child"></div><div class="sk-circle2 sk-child"></div><div class="sk-circle3 sk-child"></div><div class="sk-circle4 sk-child"></div><div class="sk-circle5 sk-child"></div><div class="sk-circle6 sk-child"></div><div class="sk-circle7 sk-child"></div><div class="sk-circle8 sk-child"></div><div class="sk-circle9 sk-child"></div><div class="sk-circle10 sk-child"></div><div class="sk-circle11 sk-child"></div><div class="sk-circle12 sk-child"></div></div>'
    );
    var data = {};
    var cardId = $(this).data("id");
    data.cardId = cardId;
    data.action = USERPLACE_PAYMENT_AJAX_DATA.action;
    data.nonce = USERPLACE_PAYMENT_AJAX_DATA.nonce;
    data.action_type = "delete_card";
    jQuery.post(USERPLACE_PAYMENT_AJAX_DATA.admin_url, data, function (
      response
    ) {
      window.location.reload();
    });
  });
  $(".card-list").on("click", ".userplace-default-card", function (e) {
    e.preventDefault();
    $(this).html(
      '<div class="sk-circle"><div class="sk-circle1 sk-child"></div><div class="sk-circle2 sk-child"></div><div class="sk-circle3 sk-child"></div><div class="sk-circle4 sk-child"></div><div class="sk-circle5 sk-child"></div><div class="sk-circle6 sk-child"></div><div class="sk-circle7 sk-child"></div><div class="sk-circle8 sk-child"></div><div class="sk-circle9 sk-child"></div><div class="sk-circle10 sk-child"></div><div class="sk-circle11 sk-child"></div><div class="sk-circle12 sk-child"></div></div>'
    );
    var data = {};
    var cardId = $(this).data("id");
    data.cardId = cardId;
    data.action = USERPLACE_PAYMENT_AJAX_DATA.action;
    data.nonce = USERPLACE_PAYMENT_AJAX_DATA.nonce;
    data.action_type = "make_default_card";
    jQuery.post(USERPLACE_PAYMENT_AJAX_DATA.admin_url, data, function (
      response
    ) {
      window.location.reload();
    });
  });

  $(".userplace-close-icon").on("click", function (e) {
    e.preventDefault();
    $(".userplace-message-wrapper").hide();
    window.history.pushState(
      "",
      "",
      USERPLACE_PAYMENT_AJAX_DATA.site_url + "/console"
    );
  });
  $(".userplace-close-icon").on("click", function (e) {
    e.preventDefault();
    $(".userplace-welcome").hide();
  });

  if ($(".userplace_user__tab-content").length) {
    $(".userplace_user__tab-content").eq(0).fadeIn();
  }
  if ($(".userplace_user__tab-header-list a").length) {
    $(".userplace_user__tab-header-list a").on("click", function (e) {
      e.preventDefault();
      var target = $(this).attr("href");
      $(".userplace_user__tab-header-list").removeClass("active");
      $(this).parent().addClass("active");
      $(".userplace_user__tab-content").fadeOut();
      $(target).fadeIn();
    });
  }
  if ($("#userplace_user_location_field")) {
    userplaceInitMapField();
  }

  function userplaceInitMapField() {
    let mapSelector = $("#userplace_user_location_field");
    let preValueSelector = $("#user_working_location");
    let preValue = {};
    try {
      preValue = JSON.parse(preValueSelector.val());
    } catch (e) {}
    const { lat, lng } = userplaceSetValues(preValue);
    if (mapSelector.get(0)) {
      mapSelector
        .geocomplete({
          map: ".map_canvas",
          types: ["geocode", "establishment"],
          markerOptions: {
            draggable: true,
            icon: USERPLACE_PAYMENT_AJAX_DATA.image_path + "marker.png",
            position: { lat, lng },
          },
        })
        .bind("geocode:result", function (event, result) {
          let location = {
            formattedAddress: $("#userplace_user_location_field").val(),
            lat: result.geometry.location.lat(),
            lng: result.geometry.location.lng(),
          };
          console.log(location, "location");
          const address_components = result.address_components;
          const { city, country, country_short } = userplaceGetCityAndCounty(
            address_components
          );
          location = { ...location, city, country, country_short };
          userplaceSetValues(location);
          preValueSelector.val(JSON.stringify(location));
        })
        .bind("geocode:dragged", function (event, marker) {
          var geocoder = new google.maps.Geocoder();
          const latlng = {
            lat: Number(marker.lat().toFixed(4)),
            lng: Number(marker.lng().toFixed(4)),
          };
          geocoder.geocode({ latLng: latlng }, function (results, status) {
            if (results[0] && results[0].formatted_address) {
              let location = {
                lat: latlng.lat,
                lng: latlng.lng,
                formattedAddress: results[0].formatted_address,
              };
              const address_components = results[0].address_components;
              console.log(address_components, "address_components");
              const {
                city,
                country,
                country_short,
              } = userplaceGetCityAndCounty(address_components);
              location = { ...location, city, country, country_short };
              userplaceSetValues(location);
              preValueSelector.val(JSON.stringify(location));
            }
          });
        });
      let map = mapSelector.geocomplete("map");
      const center = new google.maps.LatLng(lat, lng);
      map.setCenter(center);
      map.setZoom(14);
    }
  }
  function userplaceSetValues(location) {
    let mapSelector = $("#userplace_user_location_field");
    const citySelector = $("#map_city");
    const countrySelector = $("#map_country");
    const latSelector = $("#map_lat");
    const lngSelector = $("#map_lng");
    const lat = location && location.lat ? location.lat : 40.785091;
    const lng = location && location.lng ? location.lng : -73.968285;
    const city = location && location.city ? location.city : "New York";
    const country =
      location && location.country ? location.country : "United States";
    const formattedAddress =
      location && location.formattedAddress
        ? location.formattedAddress
        : "Central Park, New York, NY, USA";
    mapSelector.val(formattedAddress);
    citySelector.val(city);
    countrySelector.val(country);
    latSelector.val(lat);
    lngSelector.val(lng);
    return { lat, lng };
  }

  function userplaceGetCityAndCounty(address_components) {
    let address = { city: "", country: "", country_short: "" };
    for (let i = 0; i < address_components.length; i++) {
      if (address_components[i].types.length) {
        switch (address_components[i].types[0]) {
          case "locality":
            address.city = address_components[i].long_name;
            break;
          case "administrative_area_level_2":
            if (!address.city) {
              address.city = address_components[i].long_name;
            }
            break;
          case "administrative_area_level_1":
            if (!address.city) {
              address.city = address_components[i].long_name;
            }
            break;
          case "country":
            address.country = address_components[i].long_name;
            address.country_short = address_components[i].short_name;
            break;
          default:
            break;
        }
      }
    }
    return address;
  }

  $("#user_custom_gravatar").on("click", function () {
    $("#user_custom_gravatar_upload").click();
  });
  $("#user_banner_image").on("click", function () {
    $("#user_banner_image_upload").click();
  });

  $("#user_custom_gravatar_upload").on("change", function () {
    const self = this;
    if (self.files && self.files[0]) {
      var reader = new FileReader();
      reader.onload = function (e) {
        $("#user_custom_gravatar").attr("src", e.target.result);
      };
      reader.readAsDataURL(self.files[0]);
    }
  });
  $("#user_banner_image_upload").on("change", function () {
    const self = this;
    if (self.files && self.files[0]) {
      var reader = new FileReader();
      reader.onload = function (e) {
        $("#user_banner_image").attr("src", e.target.result);
      };
      reader.readAsDataURL(self.files[0]);
    }
  });
});
