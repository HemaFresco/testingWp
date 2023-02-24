/* global rtcl, toastr, rtclUnblock */
(function ($) {
  $(document).on('click', '.mark-as-sold', function (e) {
    e.preventDefault();

    var _target = this,
        _self = $(_target),
        data = {
      action: 'rtcl_mark_as_sold_unsold',
      post_id: parseInt(_self.attr("data-id"), 10),
      __rtcl_wpnonce: rtcl.__rtcl_wpnonce
    };

    if (data.post_id && !_self.hasClass('working')) {
      $.ajax({
        url: rtcl.ajaxurl,
        data: data,
        type: "POST",
        beforeSend: function beforeSend() {
          _self.addClass('working');
        },
        success: function success(res) {
          _self.removeClass('working');

          res.target = _target;

          if (res.success) {
            _self.attr('data-title', res.data.text).attr('data-tooltip', res.data.text).html(res.data.text);

            if (res.data.type === 'sold') {
              _self.addClass('sold');
            } else {
              _self.removeClass('sold');
            }
          }

          $(document).trigger('rtcl.mark_as_sold', res);
        },
        error: function error(e) {
          $(document).trigger('rtcl.mark_as_sold.error', {
            listing_id: data.post_id,
            target: _target
          });

          _self.removeClass('working');
        }
      });
    }
  });

  if ($.fn.validate) {
    // Comment validation
    $(".rtcl #commentform").validate({
      submitHandler: function submitHandler(form) {
        var f = $(form),
            $rating = f.find('#rating'),
            ratingWrap = $rating.parent('.form-group'),
            rating = $rating.val(),
            responseWrapper = $('<div class="alert" />'),
            comments = $('#comments'),
            commentlist = $('.comment-list'),
            cancelreplylink = $('#cancel-comment-reply-link'),
            button = f.find('.btn');
        var addedCommentHTML;

        if ($rating.length > 0 && !rating) {
          ratingWrap.addClass('has-danger');
          ratingWrap.find('.with-errors').remove();
          ratingWrap.append('<div class="with-errors help-block">' + rtcl.i18n_required_rating_text + '</div>');
          return false;
        } // Post via AJAX


        var fromData = new FormData(form);
        fromData.append('action', 'rtcl_ajax_submit_comment');
        $.ajax({
          url: rtcl.ajaxurl,
          data: fromData,
          type: 'POST',
          dataType: 'json',
          cache: false,
          processData: false,
          contentType: false,
          beforeSend: function beforeSend() {
            $('<span class="rtcl-icon-spinner animate-spin"></span>').insertAfter(f.find('.btn'));
            button.val('Loading...').prop("disabled", true);
            f.next('.alert').remove();
          },
          success: function success(response) {
            f.find('.btn').next('.rtcl-icon-spinner').remove();
            button.prop("disabled", false);
            responseWrapper.html(response.message).insertAfter(f);

            if (response.error) {
              responseWrapper.addClass('alert-danger');
            } else {
              responseWrapper.addClass('alert-success');

              if (response.comment_id) {
                $("#li-comment-" + response.comment_id).slideUp(250, function () {
                  $(this).remove();
                });
              }

              if (commentlist.length > 0) {
                commentlist.append(response.comment_html);
              } else {
                // if no comments yet
                addedCommentHTML = '<ol class="comment-list">' + response.comment_html + '</ol>';
                comments.append($(addedCommentHTML));
              }

              form.reset();
              f.find('p.stars').removeClass('selected');
            }
          },
          complete: function complete() {
            // what to do after a comment has been added
            button.val('Submit').prop("disabled", false);
          },
          error: function error(request, status, _error) {
            f.next('.rtcl-icon-spinner').remove();
            button.val('Submit').prop("disabled", false);

            if (status === 500) {
              alert('Error while adding comment');
            } else if (status === 'timeout') {
              alert('Error: Server doesn\'t respond.');
            } else {
              // process WordPress errors
              var wpErrorHtml = request.responseText.split("<p>"),
                  wpErrorStr = wpErrorHtml[1].split("</p>");
              alert(wpErrorStr[0]);
            }
          }
        });
        return false;
      }
    });
    $(".rtcl-sub-item[data-gateway=authorizenet] #rtcl-sub-update-payment").validate({
      submitHandler: function submitHandler(form) {
        var formData = new FormData(form);
        formData.append('action', 'rtcl_subscription_update_payment');
        formData.append('__rtcl_wpnonce', rtcl.__rtcl_wpnonce);
        var wrap = $(form).parents('.rtcl-sub-item');
        $.ajax({
          url: rtcl.ajaxurl,
          data: formData,
          type: 'POST',
          dataType: 'json',
          cache: false,
          processData: false,
          contentType: false,
          beforeSend: function beforeSend() {
            wrap.rtclBlock();
          },
          success: function success(res) {
            console.log(res);

            if (res.success) {
              form.reset();
              wrap.find('.sub-payment-update-wrap').slideUp('slow');
              toastr.success(res.data.message);
            } else {
              toastr.error(res.data);
            }
          },
          complete: function complete() {
            wrap.rtclUnblock();
          },
          error: function error(request, status, _error2) {
            toastr.error('Error while updating subscription');
          }
        });
        return false;
      }
    });
  }

  $(".rtcl-rating-filter .ui-link-tree-item").on('click', function () {
    var $self = $(this),
        value = $self.data('id') || 0,
        wrap = $self.parents('.ui-accordion-content'),
        $input = wrap.find('input[type=hidden]');

    if ($self.hasClass('selected')) {
      wrap.find('li').removeClass('selected');
      $input.val('');
    } else {
      wrap.find('li').removeClass('selected');
      $self.addClass('selected');
      $input.val(value);
    }

    $self.closest('form').submit();
  });
  $(document).on('click', '.rtcl-subs-reports .rtcl-sub-cancel', function (e) {
    e.preventDefault();

    var _this = $(this);

    var id = _this.data('id');

    if (id) {
      if (confirm(rtcl.confirm_text)) {
        var wrap = _this.parents('.rtcl-sub-item');

        var formData = new FormData();
        formData.append('action', 'rtcl_subscription_cancel');
        formData.append('id', id);
        formData.append('__rtcl_wpnonce', rtcl.__rtcl_wpnonce);
        $.ajax({
          url: rtcl.ajaxurl,
          data: formData,
          type: 'POST',
          dataType: 'json',
          cache: false,
          processData: false,
          contentType: false,
          beforeSend: function beforeSend() {
            wrap.rtclBlock();
          },
          success: function success(res) {
            if (res.success === true) {
              $(document).find('.rtcl-sub-item[data-id=' + id + ']').remove();
              toastr.success(res.data.message);
            } else {
              toastr.error(res.data);
            }
          },
          complete: function complete() {
            wrap.rtclUnblock();
          },
          error: function error(request, status, _error3) {
            toastr.error('Error while canceling subscription');
          }
        });
      }
    } else {
      toastr.error('Subscription is not defined');
    }

    return false;
  }).on('click', '.rtcl-subs-reports .update-card-info', function (e) {
    e.preventDefault();
    $(this).parents('.rtcl-subs-reports').find('.sub-payment-update-wrap').slideToggle('slow');
    return false;
  }); // Builder Content visible 
  // $(window).on('load', function(){
  //     $('.builder-content').removeClass('content-invisible');
  // });
})(jQuery);
