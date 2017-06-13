 var $j = jQuery.noConflict();
var calendar = {

  init: function() {

    var mon = 'Mon';
    var tue = 'Tue';
    var wed = 'Wed';
    var thur = 'Thur';
    var fri = 'Fri';
    var sat = 'Sat';
    var sund = 'Sun';

    /**
     * Get current date
     */
    var d = new Date();
    var strDate = yearNumber + "/" + (d.getMonth() + 1) + "/" + d.getDate();
    var yearNumber = (new Date).getFullYear();
    /**
     * Get current month and set as '.current-month' in title
     */
    var monthNumber = d.getMonth() + 1;

    function GetMonthName(monthNumber) {
      var months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
      return months[monthNumber - 1];
    }

    setMonth(monthNumber, mon, tue, wed, thur, fri, sat, sund);

    function setMonth(monthNumber, mon, tue, wed, thur, fri, sat, sund) {
      $j('.month').text(GetMonthName(monthNumber) + ' ' + yearNumber);
      $j('.month').attr('data-month', monthNumber);
      printDateNumber(monthNumber, mon, tue, wed, thur, fri, sat, sund);
    }

   $j('.btn-next').on('click', function(e) {
      var monthNumber = $j('.month').attr('data-month');
      if (monthNumber > 11) {
        $j('.month').attr('data-month', '0');
        var monthNumber = $j('.month').attr('data-month');
        yearNumber = yearNumber + 1;
        setMonth(parseInt(monthNumber) + 1, mon, tue, wed, thur, fri, sat, sund);
      } else {
        setMonth(parseInt(monthNumber) + 1, mon, tue, wed, thur, fri, sat, sund);
      };
    });

   $j('.btn-prev').on('click', function(e) {
      var monthNumber = $j('.month').attr('data-month');
      if (monthNumber < 2) {
        $j('.month').attr('data-month', '13');
        var monthNumber = $j('.month').attr('data-month');
        yearNumber = yearNumber - 1;
        setMonth(parseInt(monthNumber) - 1, mon, tue, wed, thur, fri, sat, sund);
      } else {
        setMonth(parseInt(monthNumber) - 1, mon, tue, wed, thur, fri, sat, sund);
      };
    });

    /**
     * Get all dates for current month
     */

    function printDateNumber(monthNumber, mon, tue, wed, thur, fri, sat, sund) {

      $j($j('tbody.event-calendar tr')).each(function(index) {
        $j(this).empty();
      });

      $j($j('thead.event-days tr')).each(function(index) {
        $j(this).empty();
      });

      function getDaysInMonth(month, year) {
        // Since no month has fewer than 28 days
        var date = new Date(year, month, 1);
        var days = [];
        while (date.getMonth() === month) {
          days.push(new Date(date));
          date.setDate(date.getDate() + 1);
        }
        return days;
      }

      i = 0;

      setDaysInOrder(mon, tue, wed, thur, fri, sat, sund);

      function setDaysInOrder(mon, tue, wed, thur, fri, sat, sund) {
        var monthDay = getDaysInMonth(monthNumber - 1, yearNumber)[0].toString().substring(0, 3);
        if (monthDay === 'Mon') {
          $j('thead.event-days tr').append('<td>' + mon + '</td><td>' + tue + '</td><td>' + wed + '</td><td>' + thur + '</td><td>' + fri + '</td><td>' + sat + '</td><td>' + sund + '</td>');
        } else if (monthDay === 'Tue') {
          $j('thead.event-days tr').append('<td>' + tue + '</td><td>' + wed + '</td><td>' + thur + '</td><td>' + fri + '</td><td>' + sat + '</td><td>' + sund + '</td><td>' + mon + '</td>');
        } else if (monthDay === 'Wed') {
          $j('thead.event-days tr').append('<td>' + wed + '</td><td>' + thur + '</td><td>' + fri + '</td><td>' + sat + '</td><td>' + sund + '</td><td>' + mon + '</td><td>' + tue + '</td>');
        } else if (monthDay === 'Thu') {
          $j('thead.event-days tr').append('<td>' + thur + '</td><td>' + fri + '</td><td>' + sat + '</td><td>' + sund + '</td><td>' + mon + '</td><td>' + tue + '</td><td>' + wed + '</td>');
        } else if (monthDay === 'Fri') {
          $j('thead.event-days tr').append('<td>' + fri + '</td><td>' + sat + '</td><td>' + sund + '</td><td>' + mon + '</td><td>' + tue + '</td><td>' + wed + '</td><td>' + thur + '</td>');
        } else if (monthDay === 'Sat') {
          $j('thead.event-days tr').append('<td>' + sat + '</td><td>' + sund + '</td><td>' + mon + '</td><td>' + tue + '</td><td>' + wed + '</td><td>' + thur + '</td><td>' + fri + '</td>');
        } else if (monthDay === 'Sun') {
          $j('thead.event-days tr').append('<td>' + sund + '</td><td>' + mon + '</td><td>' + tue + '</td><td>' + wed + '</td><td>' + thur + '</td><td>' + fri + '</td><td>' + sat + '</td>');
        }
      };
      $j(getDaysInMonth(monthNumber - 1, yearNumber)).each(function(index) {
        var index = index + 1;
        if (index < 8) {
          $j('tbody.event-calendar tr.1').append('<td date-month="' + monthNumber + '" date-day="' + index + '" date-year="' + yearNumber + '">' + index + '</td>');
        } else if (index < 15) {
          $j('tbody.event-calendar tr.2').append('<td date-month="' + monthNumber + '" date-day="' + index + '" date-year="' + yearNumber + '">' + index + '</td>');
        } else if (index < 22) {
          $j('tbody.event-calendar tr.3').append('<td date-month="' + monthNumber + '" date-day="' + index + '" date-year="' + yearNumber + '">' + index + '</td>');
        } else if (index < 29) {
          $j('tbody.event-calendar tr.4').append('<td date-month="' + monthNumber + '" date-day="' + index + '" date-year="' + yearNumber + '">' + index + '</td>');
        } else if (index < 32) {
          $j('tbody.event-calendar tr.5').append('<td date-month="' + monthNumber + '" date-day="' + index + '" date-year="' + yearNumber + '">' + index + '</td>');
        }
        i++;
      });
      var date = new Date();
      var month = date.getMonth() + 1;
      var thisyear = new Date().getFullYear();
      setCurrentDay(month, thisyear);
      setEvent();
      displayEvent();
    }

    /**
     * Get current day and set as '.current-day'
     */
    function setCurrentDay(month, year) {
      var viewMonth = $j('.month').attr('data-month');
      var eventYear =$j('.event-days').attr('date-year');
      if (parseInt(year) === yearNumber) {
        if (parseInt(month) === parseInt(viewMonth)) {
          $j('tbody.event-calendar td[date-day="' + d.getDate() + '"]').addClass('current-day');
        }
      }
    };

    /**
     * Add class '.active' on calendar date
     */
   $j('tbody td').on('click', function(e) {
      if ($j(this).hasClass('event')) {
        $j('tbody.event-calendar td').removeClass('active');
       $j(this).addClass('active');
      } else {
       $j('tbody.event-calendar td').removeClass('active');
      };
    });

    /**
     * Add '.event' class to all days that has an event
     */
    function setEvent() {
      $j('.day-event').each(function(i) {
        var eventMonth = $j(this).attr('date-month');
        var eventDay = $j(this).attr('date-day');
        var eventYear = $j(this).attr('date-year');
        var eventClass = $j(this).attr('event-class');
        if (eventClass === undefined) eventClass = 'event';
        else eventClass = 'event ' + eventClass;

        if (parseInt(eventYear) === yearNumber) {
          $j('tbody.event-calendar tr td[date-year="' + eventYear + '"][date-month="' + eventMonth + '"][date-day="' + eventDay + '"]').addClass(eventClass);
        }
      });
    };

    /**
     * Get current day on click in calendar
     * and find day-event to display
     */
    function displayEvent() {
      $j('tbody.event-calendar td').on('click', function(e) {
        $j('.day-event').slideUp('fast');
        var yearEvent = $j(this).attr('date-year');
        var monthEvent = $j(this).attr('date-month');
        var dayEvent =$j(this).text();
        $j('.day-event[date-year="' + yearEvent + '"][date-month="' + monthEvent + '"][date-day="' + dayEvent + '"]').slideDown('fast');
      });
    };

    /**
     * Close day-event
     */
    $j('.close').on('click', function(e) {
      $j(this).parent().slideUp('fast');
    });

    /**
     * Save & Remove to/from personal list
     */
   $j('.save').click(function() {
      if (this.checked) {
       $j(this).next().text('Remove from personal list');
        var eventHtml = $j(this).closest('.day-event').html();
        var eventMonth = $j(this).closest('.day-event').attr('date-month');
        var eventDay = $j(this).closest('.day-event').attr('date-day');
        var eventNumber = $j(this).closest('.day-event').attr('data-number');
        $j('.person-list').append('<div class="day" date-month="' + eventMonth + '" date-day="' + eventDay + '" data-number="' + eventNumber + '" style="display:none;">' + eventHtml + '</div>');
        $j('.day[date-month="' + eventMonth + '"][date-day="' + eventDay + '"]').slideDown('fast');
        $j('.day').find('.close').remove();
       $j('.day').find('.save').removeClass('save').addClass('remove');
        $j('.day').find('.remove').next().addClass('hidden-print');
        remove();
        sortlist();
      } else {
        $j(this).next().text('Save to personal list');
        var eventMonth = $j(this).closest('.day-event').attr('date-month');
        var eventDay = $j(this).closest('.day-event').attr('date-day');
        var eventNumber = $j(this).closest('.day-event').attr('data-number');
        $j('.day[date-month="' + eventMonth + '"][date-day="' + eventDay + '"][data-number="' + eventNumber + '"]').slideUp('slow');
        setTimeout(function() {
          $j('.day[date-month="' + eventMonth + '"][date-day="' + eventDay + '"][data-number="' + eventNumber + '"]').remove();
        }, 1500);
      }
    });

    function remove() {
      $j('.remove').click(function() {
        if (this.checked) {
          $j(this).next().text('Remove from personal list');
          var eventMonth = $j(this).closest('.day').attr('date-month');
          var eventDay = $j(this).closest('.day').attr('date-day');
          var eventNumber = $j(this).closest('.day').attr('data-number');
          $j('.day[date-month="' + eventMonth + '"][date-day="' + eventDay + '"][data-number="' + eventNumber + '"]').slideUp('slow');
         $j('.day-event[date-month="' + eventMonth + '"][date-day="' + eventDay + '"][data-number="' + eventNumber + '"]').find('.save').attr('checked', false);
          $j('.day-event[date-month="' + eventMonth + '"][date-day="' + eventDay + '"][data-number="' + eventNumber + '"]').find('span').text('Save to personal list');
          setTimeout(function() {
            $j('.day[date-month="' + eventMonth + '"][date-day="' + eventDay + '"][data-number="' + eventNumber + '"]').remove();
          }, 1500);
        }
      });
    }

    /**
     * Sort personal list
     */
    function sortlist() {
      var personList = $j('.person-list');

      personList.find('.day').sort(function(a, b) {
        return +a.getAttribute('date-day') - +b.getAttribute('date-day');
      }).appendTo(personList);
    }

    /**
     * Print button
     */
    $j('.print-btn').click(function() {
      window.print();
    });

  },
};

$j(document).ready(function() {
  calendar.init();
});