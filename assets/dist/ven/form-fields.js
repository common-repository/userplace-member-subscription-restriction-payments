jQuery(document).ready(function($) {
  init();
  function init() {
    trashPost();
    initThumbnailPicker();
    initVideoThumbnailPicker();
    initListingLogo();
    initGalleryPicker();
    removeGalleryImage();
    removeThumbnail();
    initKeyValueRepeater();
    initFaq();
    initOpeningHour();
    initTaxonomyTextSelectbox();
    initTaxonomySelect();
    switchInit();
    initMapField();
    sortable();
    popOVer();
    textEditor();
  }

  function textEditor() {
    $("#post_content").jqte();
    $("#faq_description").jqte();
  }

  function sortable() {
    $("#sortable").sortable({
      handle: ".handle",
      placeholder: "repeating-input-placeholder"
    });
  }

  function trashPost() {
    $("button#trashPost").on("click", function(event) {
      event.preventDefault();
      const that = $(this);
      Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!"
      }).then(result => {
        if (result.value) {
          const id = that.attr("data-id");
          that.parents(".listingGrid").remove();
          $.ajax({
            type: "post",
            dataType: "json",
            url: UFS_AJAX_DATA.ajax_url,
            data: {
              action: UFS_AJAX_DATA.action,
              action_type: "trash_post",
              nonce: UFS_AJAX_DATA.nonce,
              id
            },
            success: function(response) {
              // console.log(response);
            },
            error: function(request, status, error) {
              console.log(request.responseText);
            }
          });
        }
      });
    });
  }

  function initTaxonomySelect() {
    $(".taxonomySelect").select2();
  }

  function switchInit() {
    var switchStatus = false;
    $("#allowedForKidsSwitch").on("change", function() {
      if ($(this).is(":checked")) {
        switchStatus = $(this).is(":checked");
        $("input#allowedForKids").val(switchStatus);
      } else {
        switchStatus = $(this).is(":checked");
        $("input#allowedForKids").val(switchStatus);
      }
    });
  }

  function initTaxonomyTextSelectbox(params) {
    let items = [];
    let selectedItems = [];
    let allItems = [];
    let taxonomy = {};
    let flag = false;
    try {
      allItems =
        typeof UFS_TAXONOMY_DATA !== "undefined"
          ? JSON.parse(UFS_TAXONOMY_DATA.terms)
          : [];
      taxonomy =
        typeof UFS_TAXONOMY_DATA !== "undefined"
          ? JSON.parse(UFS_TAXONOMY_DATA.taxonomy)
          : {};
      selectedItems =
        typeof UFS_TAXONOMY_DATA !== "undefined"
          ? JSON.parse(UFS_TAXONOMY_DATA.prevalue)
          : [];
    } catch (error) {
      console.log(error, "error");
    }
    render(items, selectedItems);
    function render(items, selectedItems) {
      if ($("#autocomplete_list_builder").get(0)) {
        const builderTemplate = _.template(
          $("#autocomplete_list_builder").html()
        )({
          items,
          selectedItems
        });
        $("#autocomplete_list_preview").html(builderTemplate);
      }
      dropDown();
      if ($("#selected_list_builder").get(0)) {
        const builderTemplate = _.template($("#selected_list_builder").html())({
          selectedItems
        });
        $("#selected_list_preview").html(builderTemplate);
      }
      loadAfterRenderEvents();
      const selectedSlug = selectedItems.map(item => {
        return JSON.parse(taxonomy.hierarchical) === true
          ? item.term_id
          : item.slug;
      });
      $("input#taxonomy_selectbox_data").val(selectedSlug);
    }
    function loadAfterRenderEvents() {
      $("button#remove_selected_term").on("click", function(event) {
        event.preventDefault();
        const that = $(this);
        const slug = that.attr("data-slug");
        const index = selectedItems.findIndex(item => item.slug === slug);
        selectedItems.splice(index, 1);
        render(items, selectedItems);
      });
      $(".autocomplete_list").on("mousedown", function(event) {
        event.preventDefault();
        const slug = $(this).attr("data-slug");
        const selectedItem = allItems.find(element => element.slug === slug);
        const index = selectedItems.findIndex(
          item => item.slug === selectedItem.slug
        );
        if (index === -1) {
          selectedItems.push(selectedItem);
        } else {
          selectedItems.splice(index, 1);
        }
        $("#taxonomy_selectbox").blur();
        render(items, selectedItems);
      });

      $("#taxonomy_selectbox").on("blur", function() {
        $("#autocomplete_list_preview").hide();
      });

      $("#taxonomy_selectbox").focus(function() {
        items = !items.length ? allItems : items;
        render(items, selectedItems);
        $("#autocomplete_list_preview").show();
      });
    }
    $("input#taxonomy_selectbox").on("keyup", function(event) {
      event.preventDefault();
      const that = $(this);
      const value = that.val();
      if (value === "") {
        items = [];
      } else {
        items = allItems.filter(element =>
          element.name.toLowerCase().includes(value.toLowerCase())
        );
      }
      render(items, selectedItems);
    });
  }

  function initOpeningHour() {
    let items = [];
    $valueSelector = $("input#openingHours");
    try {
      items = JSON.parse($valueSelector.val());
    } catch (e) {}
    render(items);
    function render(items) {
      if ($("#openingHourBuilder").get(0)) {
        const builderTemplate = _.template($("#openingHourBuilder").html())({
          items
        });
        $("#openingHourPreview").html(builderTemplate);
        loadAfterRenderEvents();
        $valueSelector.val(JSON.stringify(items));
      }
    }
    function loadAfterRenderEvents() {
      $("button#removeOpeningHour").on("click", function(event) {
        event.preventDefault();
        const that = $(this);
        const day = that.attr("data-day");
        console.log(items, "items");
        const index = _.findIndex(items, { day });
        console.log(day, index, "index");
        items.splice(index, 1);
        render(items);
      });
      $("#openingHourPreview").sortable({
        handle: ".handle",
        placeholder: "ui-state-highlight",
        forcePlaceholderSize: true,
        update: function(events, ui) {
          let sortedItemsId = [];
          $("input.openingHourId").each(function(index, element) {
            const elementID = $(this).data("id");
            sortedItemsId.push(elementID);
          });
          items.sort(function(a, b) {
            return sortedItemsId.indexOf(a.id) - sortedItemsId.indexOf(b.id);
          });
          render(items);
        }
      });
    }
    $("button#addNewOpeningHour").on("click", function(event) {
      event.preventDefault();
      const that = $(this);
      const day = $("select#openingHourDay")
        .children("option:selected")
        .val();
      const from = $("select#openingHourFrom")
        .children("option:selected")
        .val();
      const to = $("select#openingHourTo")
        .children("option:selected")
        .val();
      index = _.findIndex(items, { day });
      if (index === -1) {
        items.push({
          id: new Date().getTime(),
          day,
          duration: from + "-" + to
        });
        render(items);
      } else {
        alert("Sorry!" + day + " Already Added");
      }
    });
    $valueSelector.val(JSON.stringify(items));
  }

  function initKeyValueRepeater() {
    const repeatFieldValueSelector = $("input#repeat-field-value");
    let items = [];
    try {
      items = JSON.parse(repeatFieldValueSelector.val());
    } catch (error) {}

    render(items);

    function render(items) {
      if ($("#customDataBuilder").get(0)) {
        const builderTemplate = _.template($("#customDataBuilder").html())({
          items
        });
        $("#customDataPreview").html(builderTemplate);
        loadAfterRenderEvents();
        repeatFieldValueSelector.val(JSON.stringify(items));
      }
    }

    function loadAfterRenderEvents() {
      $("#customDataPreview input").on("change", function() {
        const that = $(this);
        const id = that.attr("data-id");
        const value = that.val();
        const name = that.attr("name");
        const index = _.findIndex(items, { id: parseInt(id, 10) });
        if (index != -1) {
          items[index][name] = value;
        }
        repeatFieldValueSelector.val(JSON.stringify(items));
      });
      $("button#remove-meta-field").on("click", function(event) {
        event.preventDefault();
        const that = $(this);
        const id = that.attr("data-id");
        const index = _.findIndex(items, { id: parseInt(id, 10) });
        items.splice(index, 1);
        render(items);
      });
      $(".sortableKeyValue").sortable({
        handle: ".handle",
        placeholder: "ui-state-highlight",
        forcePlaceholderSize: true,
        update: function(event, ui) {
          let sortedItemsId = [];
          $("input.custom_key").each(function(index, element) {
            const elementID = $(this).data("id");
            sortedItemsId.push(elementID);
          });
          items.sort(function(a, b) {
            return sortedItemsId.indexOf(a.id) - sortedItemsId.indexOf(b.id);
          });
          render(items);
        }
      });
      repeatFieldValueSelector.val(JSON.stringify(items));
    }

    $("button#add-new-meta-field").on("click", function(event) {
      event.preventDefault();
      const key = $("#repeater_key").val();
      const value = $("#repeater_value").val();
      if (key !== "" && value !== "") {
        items.push({ key, value, id: new Date().getTime() });
        $("#repeater_key").val("");
        $("#repeater_value").val("");
      } else {
        alert("No empty fields allowed");
      }

      render(items);
    });
  }
  function initFaq() {
    const repeatFieldValueSelector = $("input#faqValue");
    let items = [];
    try {
      items = JSON.parse(repeatFieldValueSelector.val());
    } catch (error) {}

    render(items);

    function render(items) {
      if ($("#faqBuilder").get(0)) {
        const builderTemplate = _.template($("#faqBuilder").html())({
          items
        });
        $("#faqPreview").html(builderTemplate);
        loadAfterRenderEvents();
        repeatFieldValueSelector.val(JSON.stringify(items));
      }
    }

    function loadAfterRenderEvents() {
      $(".faqEditDescription").jqte();
      $("#faqPreview input").on("change", function() {
        console.log("called");
        const that = $(this);
        const id = that.attr("data-id");
        const value = that.val();
        const name = that.attr("name");
        const index = _.findIndex(items, { id: parseInt(id, 10) });
        if (index != -1) {
          items[index][name] = value;
        }
        repeatFieldValueSelector.val(JSON.stringify(items));
      });

      $("#faqPreview .jqte_editor").on("focusout", function() {
        const that = $(this);
        const textarea = that
          .siblings(".jqte_source")
          .children(".faqEditDescription");
        const id = textarea.attr("data-id");
        const value = textarea.val();
        const name = textarea.attr("name");
        const index = _.findIndex(items, { id: parseInt(id, 10) });
        if (index != -1) {
          items[index][name] = value;
        }
        repeatFieldValueSelector.val(JSON.stringify(items));
      });
      $("button#remove-meta-field").on("click", function(event) {
        event.preventDefault();
        const that = $(this);
        const id = that.attr("data-id");
        const index = _.findIndex(items, { id: parseInt(id, 10) });
        items.splice(index, 1);
        render(items);
      });
      $("#accordion")
        .accordion({
          collapsible: true,
          active: false,
          height: "fill",
          header: "h3",
          icons: ""
        })
        .sortable({
          items: ".sortable",
          placeholder: "ui-state-highlight",
          forcePlaceholderSize: true,
          handle: ".handle",
          update: function(events, ui) {
            let sortedItemsId = [];
            $("input.faqInput").each(function(index, element) {
              const elementID = $(this).data("id");
              sortedItemsId.push(elementID);
            });
            items.sort(function(a, b) {
              return sortedItemsId.indexOf(a.id) - sortedItemsId.indexOf(b.id);
            });
            render(items);
          }
        });
    }

    $("button#addNewFaq").on("click", function(event) {
      event.preventDefault();
      const title = $("input#faq_title").val();
      const description = $("textarea#faq_description").val();
      if (title !== "" && description !== "") {
        items.push({ title, description, id: new Date().getTime() });
        $("input#faq_title").val("");
        $("textarea#faq_description").val("");
        $("#faq_description").jqteVal("");
      } else {
        alert("No empty fields allowed");
      }
      render(items);
    });
  }

  function initMapField() {
    let mapSelector = $("#listing_map");
    let preValueSelector = $("#meta__location");
    let preValue = {};
    try {
      preValue = JSON.parse(preValueSelector.val());
    } catch (e) {}
    const { lat, lng } = setValues(preValue);
    if (mapSelector.get(0)) {
      mapSelector
        .geocomplete({
          map: ".map_canvas",
          types: ["geocode", "establishment"],
          markerOptions: {
            draggable: true,
            icon: UFS_AJAX_DATA.image_path + "marker.png",
            position: { lat, lng }
          }
        })
        .bind("geocode:result", function(event, result) {
          let location = {
            formattedAddress: $("#listing_map").val(),
            lat: result.geometry.location.lat(),
            lng: result.geometry.location.lng()
          };
          const address_components = result.address_components;
          const { city, country } = getCityAndCounty(address_components);
          location = { ...location, city, country };
          setValues(location);
          preValueSelector.val(JSON.stringify(location));
        })
        .bind("geocode:dragged", function(event, marker) {
          var geocoder = new google.maps.Geocoder();
          const latlng = {
            lat: Number(marker.lat().toFixed(4)),
            lng: Number(marker.lng().toFixed(4))
          };
          geocoder.geocode({ latLng: latlng }, function(results, status) {
            if (results[0] && results[0].formatted_address) {
              let location = {
                lat: latlng.lat,
                lng: latlng.lng,
                formattedAddress: results[0].formatted_address
              };
              const address_components = results[0].address_components;
              const { city, country } = getCityAndCounty(address_components);
              location = { ...location, city, country };
              setValues(location);
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

  function getCityAndCounty(address_components) {
    let address = { city: "", country: "" };
    for (let i = 0; i < address_components.length; i++) {
      if (address_components[i].types.length) {
        switch (address_components[i].types[0]) {
          case "locality":
            address.city = address_components[i].long_name;
            break;
          case "administrative_area_level_1":
            address.city = address_components[i].long_name;
            break;
          case "country":
            address.country = address_components[i].long_name;
            break;
          default:
            break;
        }
      }
    }
    return address;
  }

  function setValues(location) {
    let mapSelector = $("#listing_map");
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

  function removeGalleryImage() {
    $("button.gallery-image").on("click", function(e) {
      e.preventDefault();
      let image_id = $(this).data("image_id");
      let post_id = $(this).data("post_id");
      $(this)
        .parent(".gallery-wrapper")
        .remove();
      $.ajax({
        type: "post",
        dataType: "json",
        url: UFS_AJAX_DATA.ajax_url,
        data: {
          action: UFS_AJAX_DATA.action,
          action_type: "remove_gallery_image",
          image_id,
          post_id,
          nonce: UFS_AJAX_DATA.nonce
        },
        success: function(response) {
          console.log(response);
        },
        error: function(request, status, error) {
          console.log(request.responseText);
        }
      });
    });
  }
  function removeThumbnail() {
    $("button.thumbnail-image").on("click", function(e) {
      e.preventDefault();
      let post_id = $(this).data("post_id");
      let meta_key = $(this).data("meta_key");
      $(this)
        .parent(".thumbnail-wrapper")
        .remove();
      $.ajax({
        type: "post",
        dataType: "json",
        url: UFS_AJAX_DATA.ajax_url,
        data: {
          action: UFS_AJAX_DATA.action,
          action_type: "remove_thumbnail",
          post_id,
          meta_key,
          nonce: UFS_AJAX_DATA.nonce
        },
        success: function(response) {
          console.log(response);
        },
        error: function(request, status, error) {
          console.log(request.responseText);
        }
      });
    });
  }

  function initThumbnailPicker() {
    if ($("#thumbnail_image")) {
      $("#thumbnail_image").spartanMultiImagePicker({
        fieldName: "thumbnail_image",
        maxCount: 1,
        maxFileSize: 10024 * 1000,
        multiple: false,
        onSizeErr: function(index, file) {
          alert("File size too big");
        },
        onExtensionErr: function(index, file) {
          alert("Please only input png or jpg type file");
        }
      });
    }
  }
  function initVideoThumbnailPicker() {
    $("#video_thumb").spartanMultiImagePicker({
      fieldName: "video_thumb",
      maxCount: 1,
      maxFileSize: 10024 * 1000,
      multiple: false,
      onSizeErr: function(index, file) {
        alert("File size too big");
      },
      onExtensionErr: function(index, file) {
        alert("Please only input png or jpg type file");
      }
    });
  }
  function initListingLogo() {
    $("#listing_logo").spartanMultiImagePicker({
      fieldName: "listing_logo",
      maxCount: 1,
      maxFileSize: 10024 * 1000,
      multiple: false,
      onSizeErr: function(index, file) {
        alert("File size too big");
      },
      onExtensionErr: function(index, file) {
        alert("Please only input png or jpg type file");
      }
    });
  }

  function initGalleryPicker() {
    $("#gallery").spartanMultiImagePicker({
      fieldName: "gallery[]",
      maxCount: 10,
      maxFileSize: 10024 * 1000,
      multiple: true,
      onSizeErr: function(index, file) {
        alert("File size too big");
      },
      onExtensionErr: function(index, file) {
        alert("Please only input png or jpg type file");
      }
    });
  }

  function popOVer() {
    $(document).mouseup(e => {
      if (!$(".ufs_listing_grid_popover").is(e.target)) {
        $(".ufs_listing_grid_popover").removeClass("active");
      }
    });

    $(".ufs_listing_grid_popover_icon").on("click", function() {
      $(this)
        .parent()
        .toggleClass("active");
    });
  }

  function dropDown() {
    if ($(".ufs_taxonomy_selectbox").length) {
      var dropdownHeight = 250;
      var scrollTop = $(window).scrollTop();
      var bottomOfVisibleWindow = $(window).height();
      var elementOffset = $(".ufs_taxonomy_selectbox").offset().top;
      var currentElementOffset =
        scrollTop - elementOffset + bottomOfVisibleWindow;

      if ($("#autocomplete_list_preview").children().length) {
        $("#autocomplete_list_preview").css({ opacity: 1 });
      } else {
        $("#autocomplete_list_preview").css({ opacity: 0 });
      }

      if (currentElementOffset > dropdownHeight) {
        $("#autocomplete_list_preview")
          .css({ top: "100%", bottom: "auto" })
          .removeClass("dropdown_above")
          .addClass("dropdown_below");
      } else {
        $("#autocomplete_list_preview")
          .css({ bottom: "100%", top: "auto" })
          .removeClass("dropdown_below")
          .addClass("dropdown_above");
      }
    }
  }
});
