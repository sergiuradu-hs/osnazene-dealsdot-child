<?php
/**
 * Template: Newsletter Form
 * Variables:
 * - $instance_id (string): unique form wrapper id
 */
?>
<div class="ml-subscribe-form ml-subscribe-form-5762378 osn-newsletter-widget" id="<?php echo esc_attr( $instance_id ); ?>">
  <div class="row-form osn-newsletter-widget__row-form">
    <form class="ml-block-form osn-newsletter-widget__form" action="https://assets.mailerlite.com/jsonp/468545/forms/91074601514173882/subscribe" data-code="" method="post" target="_blank">
      <div class="osn-newsletter-widget__field-group">
        <div class="osn-newsletter-widget__field-padding">
          <div class="osn-newsletter-widget__field-stack">
            <div class="osn-newsletter-widget__label-row">
              <label for="<?php echo esc_attr( $instance_id ); ?>-email" class="osn-newsletter-widget__label"><?php echo esc_html__( 'Unesi svoj email', 'dealsdot-child' ); ?></label>
            </div>
            <div class="ml-form-fieldRow ml-last-item osn-newsletter-widget__field-row">
              <div class="ml-field-group ml-field-email ml-validate-email ml-validate-required osn-newsletter-widget__field-shell">
                <div class="osn-newsletter-widget__input-frame">
                  <div class="osn-newsletter-widget__input-inner">
                    <div class="osn-newsletter-widget__input-column">
                      <input
                        id="<?php echo esc_attr( $instance_id ); ?>-email"
                        class="form-control osn-newsletter-widget__input"
                        aria-label="email"
                        aria-required="true"
                        type="email"
                        data-inputmask=""
                        name="fields[email]"
                        placeholder="olivia@bla.com"
                        autocomplete="email"
                      >
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="osn-newsletter-widget__actions">
        <input type="hidden" name="ml-submit" value="1">

        <button type="submit" class="primary osn-newsletter-widget__submit"><?php echo esc_html__( 'Prijavi se', 'dealsdot-child' ); ?></button>

        <button disabled="disabled" type="button" class="loading osn-newsletter-widget__submit" hidden>
          Loading...
        </button>

        <input type="hidden" name="anticsrf" value="true">
      </div>
    </form>
  </div>

  <div class="row-success" hidden>
    <div class="ml-form-successContent">
      <h4><?php echo esc_html__( 'Hvala!', 'dealsdot-child' ); ?></h4>
      <p><?php echo esc_html__( 'Uspešno ste se prijavili na našu mailing listu.', 'dealsdot-child' ); ?></p>
    </div>
  </div>
</div>

<script>
  (function () {
    if (!window.ml_webform_success_5762378) {
      window.ml_webform_success_5762378 = function () {
        var $ = window.ml_jQuery || window.jQuery;
        if ($) {
          $('.ml-subscribe-form-5762378 .row-success').show().removeAttr('hidden');
          $('.ml-subscribe-form-5762378 .row-form').hide();
          return;
        }

        var roots = document.querySelectorAll('.ml-subscribe-form-5762378');
        roots.forEach(function (root) {
          var success = root.querySelector('.row-success');
          var form = root.querySelector('.row-form');
          if (success) {
            success.hidden = false;
          }
          if (form) {
            form.hidden = true;
          }
        });
      };
    }

    var root = document.getElementById('<?php echo esc_js( $instance_id ); ?>');
    if (!root) {
      return;
    }

    var form = root.querySelector('form');
    if (!form) {
      return;
    }

    form.addEventListener('submit', function () {
      var submit = form.querySelector('button[type="submit"]');
      var loading = form.querySelector('button.loading');
      if (submit) {
        submit.hidden = true;
      }
      if (loading) {
        loading.hidden = false;
      }
    });

    fetch('https://assets.mailerlite.com/jsonp/468545/forms/91074601514173882/track-view');
  })();
</script>
