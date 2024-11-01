<?php

/* ======= Register The Plugin Shortcode =========== */

function softech_wp_clock_shortcode($atts)
{
  $clock_css = null;
  $atts = shortcode_atts(
    array(
      'id' => '',
      'name' => null,
    ), $atts);

  //$user_shortcode = '[softech-wp-clock name="'.$atts['name'].'"]';
  global $wpdb;
  ob_start();
  $table_name = $wpdb->prefix . 'softech_clock';
  $results = $wpdb->get_results('SELECT * FROM ' . $table_name . ' WHERE id ="' . $atts['id'] . '" ');

  foreach ($results as $results) {
    $clock_css = $results->clock_css;
  }

  $clock_css = json_decode($clock_css, true);
  $map_1 = $results->time_zone;
  date_default_timezone_set($map_1);

  /*================================This Will Show The Default Clock On Frontend==================================
  ===============================================================================================================*/
  if ($clock_css['clock_style'] == 'Default') {
    ?>
    <style>
      .softech-wp-clock {
        background:
          <?php echo $clock_css['bgcolor']; ?>
        ;
      }

      .softech-wp-date-<?php echo $atts['id']; ?> {
        font-size:
          <?php echo $clock_css['date_font_size']; ?>
          px;
      }

      .softech-wp-time-<?php echo $atts['id']; ?> {
        font-size: <?php echo $clock_css['time_font_size']; ?>px;
      }

      .softech-wp-zone-<?php echo $atts['id']; ?> {
        font-size: <?php echo $clock_css['zone_font_size']; ?>px;
      }

      @media (max-width: 767px) {
        .softech-wp-time-<?php echo $atts['id']; ?> {
          font-size: initial;
        }
      }
    </style>
    <div class="softech-wp-clock" style="padding:<?php echo esc_attr($clock_css['padding']); ?>px;
                                         color:<?php echo esc_attr($clock_css['font_color']); ?>;
                                         text-align:<?php echo esc_attr($clock_css['text_align']); ?>;">
      <p class="softech-wp-date-<?php echo esc_attr($atts['id']) ?>"> <span
          id="sd-<?php echo esc_attr($atts['id']) ?>"></span></p>
      <p class="softech-wp-time-<?php echo esc_attr($atts['id']) ?>"><span
          id="st-<?php echo esc_attr($atts['id']) ?>"></span></p>
      <p class="softech-wp-zone-<?php echo esc_attr($atts['id']) ?>"><?php echo date_default_timezone_get() ?></p>
    </div>
    <script>
      function display_c_<?php echo $atts['id']; ?>() {
        var refresh = 1000; // Refresh rate in milli seconds
        mytime = setTimeout('display_ct_<?php echo $atts['id']; ?>()', refresh)
      }
      function display_ct_<?php echo $atts['id']; ?>() {
        display_c_<?php echo $atts['id']; ?>();
        var softzone = '<?php echo $map_1; ?>';
        var sd = 'sd-<?php echo $atts['id']; ?>';
        var st = 'st-<?php echo $atts['id']; ?>';
        var softdate = new Date();
        options =
        {
          year: 'numeric', month: 'long', day: 'numeric', weekday: 'long',
          timeZone: softzone
        };
        // alert(softdate.toLocaleString('en-US', options));
        document.getElementById(sd).innerHTML = softdate.toLocaleString('en-US', options);
        options =
        {
          hour: 'numeric', minute: 'numeric', second: 'numeric',
          timeZone: softzone
        };
        document.getElementById(st).innerHTML = softdate.toLocaleString('en-US', options);
      }
      window.onload = display_ct_<?php echo $atts['id']; ?>();
    </script>
    <?php
  }
  /*================================This Will Show The Flip Clock On Frontend====================================
  ===============================================================================================================*/ else if ($clock_css['clock_style'] == 'Flip Clock') { ?>
      <!-- <time>
        <div class="clock-<?php echo esc_attr($atts['id']) ?>">
          <div class="dial-container dial-container--hh js-clock-<?php echo esc_attr($atts['id']) ?>" data-cur="9"
            data-start="0" data-end="12" data-dur="hh"></div>
          &nbsp;
          <div class="dial-container dial-container--mm js-clock-<?php echo esc_attr($atts['id']) ?>" data-cur="2"
            data-start="0" data-end="5" data-dur="mm"></div>
          <div class="dial-container dial-container--m js-clock-<?php echo esc_attr($atts['id']) ?>" data-cur="3"
            data-start="0" data-end="9" data-dur="m"></div>
          &nbsp;
          <div class="dial-container dial-container--ss js-clock-<?php echo esc_attr($atts['id']) ?>" data-cur="4"
            data-start="0" data-end="5" data-dur="ss"></div>
          <div class="dial-container dial-container--s js-clock-<?php echo esc_attr($atts['id']) ?>" data-cur="8"
            data-start="0" data-end="9" data-dur="s"></div>
        </div>
      </time>

      <script>
        function display_ct_<?php echo $atts['id']; ?>() {
          var appendEls, attachEvents, enableTransitions, nthDigit, reset, setAttributes, setClasses, startClock, tick;
          nthDigit = function (int, nth) {
            return parseInt(int.toString().substr(nth, 1));
          };
          setAttributes = function () {
            var hours, minutes, seconds, timeNow;
            var softzone = '<?php echo $map_1; ?>';
            timeNow = new Date();
            hours =
            {
              hour: 'numeric',
              timeZone: softzone
            };
            hours = timeNow.toLocaleString('en-US', hours);
            if (hours > 12) {
              hours -= 12;
            }
            jQuery('.js-clock-<?php echo esc_attr($atts['id']) ?>[data-dur="hh"]').attr('data-cur', hours + 1);
            minutes =
            {
              minute: 'numeric',
              timeZone: softzone
            };
            minutes = timeNow.toLocaleString('en-US', minutes);
            if (minutes < 10) {
              jQuery('.js-clock-<?php echo esc_attr($atts['id']) ?>[data-dur="mm"]').attr('data-cur', 1);
              jQuery('.js-clock-<?php echo esc_attr($atts['id']) ?>[data-dur="m"]').attr('data-cur', minutes + 1);
            }
            else {
              jQuery('.js-clock-<?php echo esc_attr($atts['id']) ?>[data-dur="mm"]').attr('data-cur', nthDigit(minutes, 0) + 1);
              jQuery('.js-clock-<?php echo esc_attr($atts['id']) ?>[data-dur="m"]').attr('data-cur', nthDigit(minutes, 1) + 1);
            }
            seconds =
            {
              second: 'numeric',
              timeZone: softzone
            };
            seconds = timeNow.toLocaleString('en-US', seconds);
            if (seconds < 10) {
              jQuery('.js-clock-<?php echo esc_attr($atts['id']) ?>[data-dur="ss"]').attr('data-cur', 1);
              return jQuery('.js-clock-<?php echo esc_attr($atts['id']) ?>[data-dur="s"]').attr('data-cur', seconds + 1);
            }
            else {
              jQuery('.js-clock-<?php echo esc_attr($atts['id']) ?>[data-dur="ss"]').attr('data-cur', nthDigit(seconds, 0) + 1);
              return jQuery('.js-clock-<?php echo esc_attr($atts['id']) ?>[data-dur="s"]').attr('data-cur', nthDigit(seconds, 1) + 1);
            }
          };
          tick = function ($el) {
            var $active;
            $active = $el.find('.dial--active');
            $active.removeClass('dial--active');
            $active.addClass('dial--flipped');
            $active.next().removeClass('dial--next').addClass('dial--active');
            $active.next().next().addClass('dial--next').removeClass('dial--later');
            if ($active.next().hasClass('dial--last')) {
              return setTimeout((function () {
                return reset($el);
              }), 300, $el);
            }
          };
          enableTransitions = function ($el) {
            return $el.removeClass('transitions-off');
          };
          reset = function ($el) {
            $el.addClass('transitions-off');
            $el.children().removeClass('dial--flipped');
            $el.children().removeClass('dial--active');
            $el.children().removeClass('dial--next');
            $el.children().first().addClass('dial--active');
            $el.children(':nth-child(2)').addClass('dial--next');
            $el.children(':nth-child(n+3)').addClass('dial--later');
            setTimeout((function () {
              return enableTransitions($el);
            }), 300, $el);
            tick($el);
            if ($el.attr('data-dur') === 's') {
              jQuery(document).trigger('10s');
            }
            if ($el.attr('data-dur') === 'ss') {
              jQuery(document).trigger('60s');
            }
            if ($el.attr('data-dur') === 'm') {
              jQuery(document).trigger('10m');
            }
            if ($el.attr('data-dur') === 'mm') {
              return jQuery(document).trigger('60m');
            }
          };
          setClasses = function ($el) {
            var curIndex;
            curIndex = parseInt($el.attr('data-cur'));
            $el.children(':nth-child(' + curIndex + ')').addClass('dial--active');
            $el.children(':nth-child(' + (curIndex + 1) + ')').addClass('dial--next');
            $el.children(':lt(' + curIndex + ')').addClass('dial--flipped');
            $el.children(':gt(' + curIndex + ')').addClass('dial--later');
            return tick($el);
          };
          startClock = function () {
            return setInterval(function () {
              return tick(jQuery('.js-clock-<?php echo esc_attr($atts['id']) ?>[data-dur="s"]'));
            }, 1000);
          };
          appendEls = function () {
            return jQuery('.js-clock-<?php echo esc_attr($atts['id']) ?>').each(function () {
              var $el, end, k, l, start;
              $el = jQuery(this);
              start = parseInt(jQuery(this).attr('data-start'));
              end = parseInt(jQuery(this).attr('data-end'));
              k = start;
              while (k <= end) {
                if ((k + 1) > end) {
                  l = 0;
                }
                else {
                  l = k + 1;
                }
                $el.append(`<div class="dial"><span>${k}</span><span>${l}</span></div>`);
                k++;
              }
              $el.prepend(`<div class="dial"><span>0</span><span>0</span></div>`);
              $el.append(`<div class="dial dial--last"><span>0</span><span>0</span></div>`);
              return setClasses($el);
            });
          };
          attachEvents = function () {
            jQuery('.js-clock-<?php echo esc_attr($atts['id']) ?>').on('click', function (e) {
              var $active, $el;
              e.preventDefault();
              $el = jQuery(this);
              $active = $el.find('.dial--active');
              return tick($el);
            });
            jQuery(document).on('10s', function () {
              return tick(jQuery('.js-clock-<?php echo esc_attr($atts['id']) ?>[data-dur="ss"]'));
            });
            jQuery(document).on('60s', function () {
              return tick(jQuery('.js-clock-<?php echo esc_attr($atts['id']) ?>[data-dur="m"]'));
            });
            return jQuery(document).on('10m', function () {
              tick(jQuery('.js-clock-<?php echo esc_attr($atts['id']) ?>[data-dur="mm"]'));
              jQuery(document).on('60m', function () { });
              return tick(jQuery('.js-clock-<?php echo esc_attr($atts['id']) ?>[data-dur="hh"]'));
            });
          };
          jQuery(document).ready(function () {
            setAttributes();
            appendEls();
            attachEvents();
            return startClock();
          });
        }
        window.onload = display_ct_<?php echo $atts['id']; ?>();
      </script>
      <style type="text/css">
        .transitions-off * {
          -webkit-transition: none !important;
          transition: none !important;
        }

        .dial-container {
          display: inline-block;
          position: relative;
          text-align: center;
          margin: auto;
          -webkit-perspective: 1000;
          perspective: 1000;
          height: 100px;
          width: 65px;
          cursor: default;
        }

        .dial-container.dial-container--hh {
          width: 120px;
        }

        .clock-<?php echo esc_attr($atts['id']) ?> span {
          width: 65px;
          height: 50px;
        }

        .dial {
          top: 0;
          height: 25px;
          /* overflow: hidden */
          -webkit-transition: all 0.6s;
          transition: all 0.6s;
          -webkit-transform-style: preserve-3d;
          transform-style: preserve-3d;
          position: absolute;
          z-index: 3;
          -webkit-transform-origin: 50px 50px;
          transform-origin: 50px 50px;
        }

        .clock-<?php echo esc_attr($atts['id']) ?> span {
          display: block;
          -webkit-backface-visibility: hidden;
          backface-visibility: hidden;
          position: absolute;
          top: 0;
          left: 0;
          height: 50px;
          overflow: hidden;
          background:
          <?php echo $clock_css['bgcolor']; ?>
          ;
          color:
          <?php echo esc_attr($clock_css['font_color']); ?>
          ;
          text-align: center;
          line-height: 100px;
          font-size:
          <?php echo esc_attr($clock_css['time_font_size']); ?>
            px;
          font-weight: bold;
          border-radius: 10px 10px 0 0;
        }

        .dial-container--ss span,
        .dial-container--mm span {
          text-align: right;
          padding-right: 2px;
          border-radius: 10px 0 0 0;
        }

        .dial-container--hh span {
          text-align: center;
          min-width: 120px;
        }

        .dial-container--s span,
        .dial-container--m span {
          text-align: left;
          padding-left: 2px;
          border-radius: 0 10px 0 0;
        }

        .clock-<?php echo esc_attr($atts['id']) ?> span:first-child {
          z-index: 2;
          -webkit-transform: rotateY(0deg);
          transform: rotateY(0deg);
          background-color:
          <?php echo $clock_css['bgcolor']; ?>
          ;
          color:
          <?php echo esc_attr($clock_css['font_color']); ?>
          ;
        }

        .clock-<?php echo esc_attr($atts['id']) ?> span:first-child:after {
          display: block;
          width: 100%;
          height: 1px;
          background: rgba(0, 0, 0, 0.2);
          content: ' ';
          position: absolute;
          bottom: 0;
          left: 0;
        }

        .clock-<?php echo esc_attr($atts['id']) ?> span:last-child {
          -webkit-transform: rotateY(180deg);
          transform: rotateY(180deg);
          border-radius: 0 0 10px 10px;
          line-height: 0;
          -webkit-transform: rotateX(180deg);
          transform: rotateX(180deg);
        }

        .dial-container--ss span:last-child,
        .dial-container--mm span:last-child {
          border-radius: 0 0 0 10px;
        }

        .dial-container--s span:last-child,
        .dial-container--m span:last-child {
          border-radius: 0 0 10px 0;
        }

        .dial--static,
        .dial--next {
          -webkit-transform: rotateX(0deg) !important;
          transform: rotateX(0deg) !important;
        }

        .dial--flipped {
          z-index: 2;
          -webkit-transform: rotateX(180deg) !important;
          transform: rotateX(180deg) !important;
        }

        .dial--next {
          z-index: 2;
        }

        .dial--later {
          z-index: 1;
        }

        @media (max-width: 767px) {
          .dial-container {
            width: 50px;
          }

          .dial-container.dial-container--hh {
            width: 65px;
          }

          .dial-container--hh span {
            min-width: 65px;
          }

          .clock-<?php echo esc_attr($atts['id']) ?> span {
            font-size: 40px;
          }

          .clock-<?php echo esc_attr($atts['id']) ?> span {
            width: 50px !important;
            height: 50px !important;
          }
        }
      </style> -->
  <?php }

  /*================================This Will Show The Analog Clock On Frontend==================================
  ===============================================================================================================*/ else if ($clock_css['clock_style'] == 'Analog Clock') { ?>
        <div class="clock-container-<?php echo $atts['id']; ?>">
          <div class="clock-col-<?php echo $atts['id']; ?>">
            <p class="clock-day clock-timer" id="clock_day_<?php echo esc_attr($atts['id']) ?>">
            </p>
            <p class="clock-label">
              Day
            </p>
          </div>
          <div class="clock-col-<?php echo $atts['id']; ?>">
            <p class="clock-hours clock-timer" id="clock_hours_<?php echo esc_attr($atts['id']) ?>">
            </p>
            <p class="clock-label">
              Hours
            </p>
          </div>
          <div class="clock-col-<?php echo $atts['id']; ?>">
            <p class="clock-minutes clock-timer" id="clock_minutes_<?php echo esc_attr($atts['id']) ?>">
            </p>
            <p class="clock-label">
              Minutes
            </p>
          </div>
          <div class="clock-col-<?php echo $atts['id']; ?>">
            <p class="clock-seconds clock-timer" id="clock_seconds_<?php echo esc_attr($atts['id']) ?>">
            </p>
            <p class="clock-label">
              Seconds
            </p>
          </div>
        </div>

        <script>
          function analog_c_<?php echo $atts['id']; ?>() {
            var refresh = 1000; // Refresh rate in milli seconds
            mytime = setTimeout('analog_ct_<?php echo $atts['id']; ?>()', refresh)
          }
          function analog_ct_<?php echo $atts['id']; ?>() {
            analog_c_<?php echo $atts['id']; ?>();
            var softzone = '<?php echo $map_1; ?>';
            var softdate = new Date();
            day = { weekday: 'long', timeZone: softzone };
            hour = { hour: '2-digit', hour12: false, timeZone: softzone };
            minute = { minute: 'numeric', timeZone: softzone };
            second = { second: 'numeric', timeZone: softzone };
            //alert(softdate.toLocaleString('en-US', day));
            jQuery("#clock_day_<?php echo esc_attr($atts['id']) ?>").html(softdate.toLocaleString('en-US', day).substring(0, 2));
            jQuery("#clock_hours_<?php echo esc_attr($atts['id']) ?>").html(softdate.toLocaleString('en-US', hour));
            jQuery("#clock_minutes_<?php echo esc_attr($atts['id']) ?>").html(softdate.toLocaleString('en-US', minute));
            jQuery("#clock_seconds_<?php echo esc_attr($atts['id']) ?>").html(softdate.toLocaleString('en-US', second));
          }
          window.onload = analog_ct_<?php echo $atts['id']; ?>();
        </script>
        <style type="text/css">
          .clock-container-<?php echo $atts['id']; ?> {
            justify-content:center;
            background-color:
          <?php echo $clock_css['bgcolor']; ?>
            ;
            border-radius: 5px;
            padding:
          <?php echo $clock_css['padding']; ?>
              px;
            box-shadow: 1px 1px 5px rgba(255, 255, 255, .15), 0 15px 90px 30px rgba(0, 0, 0, .25);
            display: flex;
          }
          .clock-col-<?php echo $atts['id']; ?> {
            text-align:
          <?php echo $clock_css['text_align']; ?>
            ;
            margin-right: 40px;
            margin-left: 40px;
            min-width: 90px;
            position: relative;
          }
          .clock-col-<?php echo $atts['id']; ?>:not(:last-of-type):before,
          .clock-col-<?php echo $atts['id']; ?>:not(:last-of-type):after {
            content: "";
            background-color: rgba(255, 255, 255, .3);
            height: 5px;
            width: 5px;
            border-radius: 50%;
            display: block;
            position: absolute;
            right: -42px;
          }
          .clock-col-<?php echo $atts['id']; ?>:not(:last-of-type):before {
            top: 35%;
          }
          .clock-col-<?php echo $atts['id']; ?>:not(:last-of-type):after {
            top: 50%;
          }
          .clock-timer {
            color:
          <?php echo $clock_css['font_color']; ?>
            ;
            font-size:
          <?php echo $clock_css['time_font_size']; ?>
              px;
            text-transform: uppercase;
            margin: 0;
          }
          .clock-label {
            color: rgba(255, 255, 255, .35);
            text-transform: uppercase;
            font-size: 0.7rem;
            margin-top: 10px;
          }
          @media (max-width: 825px) {
            .clock-container-<?php echo $atts['id']; ?> {
              /*flex-direction: column;*/
              padding-top: 15px;
              padding-bottom: 15px;
            }
            .clock-col-<?php echo $atts['id']; ?> {
              margin-right: 10px;
              margin-left: 10px;
              min-width: auto;
              position: initial;
            }
            .clock-col-<?php echo $atts['id']; ?>:before,
            .clock-col-<?php echo $atts['id']; ?>:after {
              display: none !important;
            }
            .clock-timer {
              font-size: initial;
            }
          }
        </style>
  <?php }
  // ==This Will Show The Analog Clock On Frontend=
   if ($clock_css['clock_style'] == 'digital-design') {
    ?>
    <div class="clock-container-<?php echo $atts['id']; ?>">
      <div class="clock-col-<?php echo $atts['id']; ?>">
        <p class="clock-day clock-timer" id="clock_day_<?php echo esc_attr($atts['id']) ?>">
        </p>
      </div>
      <div class="clock-col-<?php echo $atts['id']; ?>">
        <p class="clock-date clock-timer" id="clock_date_<?php echo esc_attr($atts['id']) ?>">
        </p>
      </div>
      <div class="clock-col-<?php echo $atts['id']; ?>">
        <p class="clock-hours clock-timer" id="clock_hours_<?php echo esc_attr($atts['id']) ?>">
        </p>
      </div>
      <div class="right-time">
        <div class="clock-col-<?php echo $atts['id']; ?>">
          <p class="clock-minutes clock-timer" id="clock_minutes_<?php echo esc_attr($atts['id']) ?>">
          </p>
        </div>
        <div class="clock-col-<?php echo $atts['id']; ?>">
          <p class="clock-seconds clock-timer" id="clock_seconds_<?php echo esc_attr($atts['id']) ?>">
          </p>
        </div>
      </div>
    </div>
    <script>
      function analog_c_<?php echo $atts['id']; ?>() {
        var refresh = 1000; // Refresh rate in milli seconds
        mytime = setTimeout('analog_ct_<?php echo $atts['id']; ?>()', refresh)
      }
      function analog_ct_<?php echo $atts['id']; ?>() {
        analog_c_<?php echo $atts['id']; ?>();
        var softzone = '<?php echo $map_1; ?>';
        var softdate = new Date();
        day = { weekday: 'long', timeZone: softzone };
        dateOptions = { year: 'numeric', month: 'long', day: 'numeric', timeZone: softzone };
        hour = { hour: '2-digit', hour12: true, timeZone: softzone };
        minute = { minute: 'numeric', timeZone: softzone };
        second = { second: 'numeric', timeZone: softzone };
        // console.log(softdate.toLocaleString('en-US', dateOptions ));
        jQuery("#clock_day_<?php echo esc_attr($atts['id']) ?>").html(softdate.toLocaleString('en-US', day).substring(0, 3));
        jQuery("#clock_date_<?php echo esc_attr($atts['id']) ?>").html(softdate.toLocaleString('en-US', dateOptions));
        jQuery("#clock_hours_<?php echo esc_attr($atts['id']) ?>").html(softdate.toLocaleString('en-US', hour));
        softdate.toLocaleString('en-US', hour) <= 9 ? jQuery("#clock_minutes_<?php echo esc_attr($atts['id']) ?>").html('0' +softdate.toLocaleString('en-US', minute)) : jQuery("#clock_minutes_<?php echo esc_attr($atts['id']) ?>").html(softdate.toLocaleString('en-US', minute));
        softdate.toLocaleString('en-US', second) <= 9 ? jQuery("#clock_seconds_<?php echo esc_attr($atts['id']) ?>").html('0'+softdate.toLocaleString('en-US', second)) :  jQuery("#clock_seconds_<?php echo esc_attr($atts['id']) ?>").html(softdate.toLocaleString('en-US', second));
      }
      window.onload = analog_ct_<?php echo $atts['id']; ?>();
    </script>
    <style type="text/css">
      .clock-container-<?php echo $atts['id']; ?> {
        justify-content:center;
        flex-wrap:wrap;
        width: fit-content;
        background-color:
      <?php echo $clock_css['bgcolor']; ?>
        ;
        border-radius: 18px;
        padding:<?php echo $clock_css['padding']; ?> px;
        box-shadow: 1px 1px 2px rgba(255, 255, 255, .15), 9px 9px 30px 0px rgba(0, 0, 0, .15);
        display: flex;
      }
      .clock-col-<?php echo $atts['id']; ?> {
        text-align:
      <?php echo $clock_css['text_align']; ?>
        ;
        height:100%;
        position: relative;
      }
      .clock-container-<?php echo $atts['id']; ?> > .clock-col-<?php echo $atts['id']; ?>:first-of-type:after{
        content : '';
        width : 1px ; 
        background : <?php echo $clock_css['font_color']; ?>;
        position: absolute;
        right:-1px;
        top:0;
        height:100%;
      }
      .clock-container-<?php echo $atts['id']; ?> > .clock-col-<?php echo $atts['id']; ?>:first-of-type,.clock-container-<?php echo $atts['id']; ?> > .clock-col-<?php echo $atts['id']; ?>:nth-of-type(2){
        border-bottom:1px solid <?php echo $clock_css['font_color']; ?>;
      }
      .clock-container-<?php echo $atts['id']; ?> > .clock-col-<?php echo $atts['id']; ?>:first-of-type{
        width: 20%;
      }
      .clock-container-<?php echo $atts['id']; ?> > .clock-col-<?php echo $atts['id']; ?>:nth-of-type(2){
        width:80%;
      }
      .clock-timer {
        color:
      <?php echo $clock_css['font_color']; ?>
        ;
        font-size:
      <?php echo $clock_css['time_font_size']; ?>
          px;
        text-transform: uppercase;
        margin: 0;
      }
      .clock-label {
        color: rgba(255, 255, 255, .35);
        text-transform: uppercase;
        font-size: 0.7rem;
        margin-top: 10px;
      }
      @media (max-width: 825px) {
        .clock-container-<?php echo $atts['id']; ?> {
          /*flex-direction: column;*/
          padding-top: 15px;
          padding-bottom: 15px;
        }
        .clock-col-<?php echo $atts['id']; ?> {
          margin-right: 10px;
          margin-left: 10px;
          min-width: auto;
          position: initial;
        }
        .clock-col-<?php echo $atts['id']; ?>:before,
        .clock-col-<?php echo $atts['id']; ?>:after {
          display: none !important;
        }
        .clock-timer {
          font-size: initial;
        }
      }
      .clock-hours{
        font-size:70px !important;
        margin-right:20px;
        font-weight: 900;
      }
      .clock-hours:after{
        content:'';
        position: absolute;
        right: 10px;
        bottom: 50%;
        transform: translate(0,50%);
        width: 1px !important;
        height:50% !important;
        background:<?php echo $clock_css['font_color']; ?>;
      }
      .clock-minutes:after {
    content: '';
    position: absolute;
    right: 0px;
    bottom: 0%;
    transform: translate(0, 50%);
    width: 100% !important;
    height: 1px !important;
    background:<?php echo $clock_css['font_color']; ?>;
}
      .clock-minutes{
        font-size: 30px;
        font-weight: 700;
      }
      .right-time{
        display: flex;
        flex-direction: column;
        height: 100%;
        transform: translate(0px, 10px);
      }
      .clock-date,.clock-day{
        font-weight: 500;
      }
      .clock-seconds{
        font-size:20px;
        font-weight: 500;
      }
    </style>
    <?php
  }
  
  $myvariable_pages = ob_get_clean();
  return $myvariable_pages;
}
add_shortcode('softech-wp-clock', 'softech_wp_clock_shortcode');
?>