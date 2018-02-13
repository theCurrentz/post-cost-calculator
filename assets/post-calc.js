jQuery(document).ready(function( $ ) {
  var dateFormat = "mm/dd/yy",
    from = $( "#from" )
      .datepicker({
        defaultDate: "+1w",
        changeMonth: true,
        numberOfMonths: 3
      })
      .on( "change", function() {
        to.datepicker( "option", "minDate", getDate( this ) );
      }),
    to = $( "#to" ).datepicker({
      defaultDate: "+1w",
      changeMonth: true,
      numberOfMonths: 3
    })
    .on( "change", function() {
      from.datepicker( "option", "maxDate", getDate( this ) );
    });

  function getDate( element ) {
    var date;
    try {
      date = $.datepicker.parseDate( dateFormat, element.value );
    } catch( error ) {
      date = null;
    }

    return date;
  }
  if (document.getElementById('total-cost-num')) {
    var tc = parseFloat(document.getElementById('total-cost-num').innerText);
  }

  $('#post-calc-equation').on('input', function() {
      postCost()
  });

  function postCost() {
    var equation = eval($('#post-calc-equation').val());
    $('#post-calc-answer').val(equation);
  }

  postCost();

  //warn authors about plagarism
  $('<div style="color: red;">Do not attempt to plagiarize. We will not pay for plagarized contributions.<br><a target="_blank" href=\"https://visual.ly/blog/plagiarism-what-it-is-what-it-isnt-and-how-to-avoid-it-in-content-marketing/\">Find out what is and what isn\'t plagiarism.</a></div>').insertBefore($('.wp-heading-inline'));

} );
