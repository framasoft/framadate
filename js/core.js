jQuery(document).ready(function() {

    window.lang = $('html').attr('lang');

    document.onkeydown = (event) => {
        if (/^(9|37|38|39|40)$/.test(event.keyCode)) { // keyboard navigation detected (tab|left|up|right|down)
            jQuery('#vote-form').removeClass('mouseable');
            jQuery('#vote-form').addClass('kb-only');
            jQuery('#t-wrap').removeClass('t-sticky');
        }
    };

    if (jQuery('#voteCSS').length === 0) {
        jQuery('head').append('<style id="voteCSS"></style>');
    }

    updateTableCSS = () => {
        let choicesColWidth = 64;
      
        jQuery('#slots').children('[id], [headers]').each(i => {
          if (jQuery('#slots').children(`:eq(${i + 1})`).width() > 90
            || jQuery('#vote-form').length === 0) {
            choicesColWidth = 100;
          } else {
            choicesColWidth = Math.max(jQuery('#slots').children(`:eq(${i + 1})`).width(), choicesColWidth);
          }
        });
      
        jQuery('#voteCSS').text(`
            #t-wrap tr > :nth-child(n + 1) {
                width: ${choicesColWidth}px;
                min-width: ${choicesColWidth}px;
                max-width: ${choicesColWidth}px;
            }
        `);
    };
    updateTableCSS();

    tableResize = function() {
        let width = jQuery('.container.ombre').width();
        if (jQuery('#t-res tbody tr:eq(0)').length > 0) {
          width = Math.min(width, jQuery('#t-res tbody tr:eq(0)').width());
        } else {
          width = Math.min(width, jQuery('#t-act tbody tr:eq(0)').width());
        }
        jQuery('#t-act thead, #t-act tbody, #t-res tbody, #t-res tfoot').width(width);
    };
    tableResize();
    jQuery(window).on('resize', function() {
        tableResize();
    });
    jQuery('#t-res tbody').on('scroll', function() {
        jQuery('#t-act thead, #t-act tbody, #t-res tfoot')
            .scrollLeft(jQuery(this).scrollLeft());
        jQuery('.focused').removeClass('focused');
    });

    jQuery('#vote-form li input').on('focus', function() {
        jQuery('#vote-form .focused').removeClass('focused');
        jQuery(this).parent().parent().parent().addClass('focused');  
    });

    // [desktop] Unfocus on mouseout
    jQuery('#vote-form td:not(.focused)').on('mouseover', () => {
        jQuery('.mouseable .focused').removeClass('focused');
    });
    // [mobile] Unfocus the last checkbox menu
    jQuery('#vote-form li input').on('focusout', function() {
        const focused = jQuery(this).parent().parent().parent();
        if (!jQuery('#vote-form').hasClass('kb-only')) {
            setTimeout(function() {
                if (focused.find('input:focus').length === 0) {
                focused.removeClass('focused');
                }
            }, 1000);
        }
    });

    // H-scroll buttons aside thead
    if (jQuery('#t-res tbody tr').length < 0) {
        jQuery('#t-res tbody').append(
            `<tr>
                
            </tr>`
        )
    }
    jQuery('.scroll-left, .scroll-right')
        .on('click', (event) => {
        const scrollScale = Math.floor(jQuery('#t-res').width() * 0.7);
        const currentPos = jQuery('#t-res tbody').scrollLeft();
        const newPos = /scroll-left/.test(event.currentTarget.className)
            ? Math.max(0, currentPos - scrollScale)
            : currentPos + scrollScale;
        jQuery('#t-res tbody').animate({scrollLeft: newPos}, 500);
        event.preventDefault();
        })
        .attr('aria-hidden', 'true');

    scrollBtnStatus = () => {
        const position = jQuery('#t-res tbody').scrollLeft();
        const atLeft = (position === 0);
        const atRight = (position >= jQuery('#t-res tbody tr:eq(0)').width() - jQuery('#t-res tbody').width());
        const scrollbar = jQuery('#t-res').width() < jQuery('#t-res tbody tr:eq(0)').width();
        jQuery('.scroll-left')[atLeft ? 'addClass' : 'removeClass']('disabled');
        jQuery('.scroll-right')[atRight ? 'addClass' : 'removeClass']('disabled');
        jQuery('.scroll-left')[!scrollbar ? 'addClass' : 'removeClass']('hidden');
        jQuery('.scroll-right')[!scrollbar ? 'addClass' : 'removeClass']('hidden');
    };
    jQuery('#t-res tbody').on('scroll', () => { scrollBtnStatus() });
    jQuery(window).on('resize', () => { scrollBtnStatus() });
    scrollBtnStatus();

    /* Results icons */
    jQuery('#t-wrap').addClass('hideNo hideIdk');

    /* Comments */
    jQuery('#commentsBtn').on('click', (event) => {
        jQuery('html, body').animate({
          scrollTop: jQuery('#comments_list').offset().top
        }, 2000);
        event.preventDefault();
    });

    /* Chart */
    if (document.getElementById('chartBtn') !== undefined) {
        /* = we are on a poll with public results */
        const res = {
            yes: {
                count: [],
                people: [],
            },
            inb: {
                count: [],
                people: [],
            },
            no: {
                count: [],
                people: [],
            },
        };
        const cols = [];

        jQuery('#addition td').each(i => {
            // Tooltip on addition
            if (i > 0 && i < jQuery('#addition td').length - 1) {
                const t = jQuery(`#slots th:eq(${i})`).attr('title');
                res.yes.people[i] = '';
                res.inb.people[i] = '';
                res.no.people[i] = '';
                cols.push(t);

                if (jQuery('#t-res tbody tr').length < 50) { // not on big polls
                    jQuery('#t-res tbody tr').each(j => {
                    res.yes.people[i] += jQuery(`#t-res tbody tr:eq(${j}) td:eq(${i-1})`).hasClass('text-success')
                        ? ` ${jQuery(`#t-res tbody tr:eq(${j})`).attr('title')},`
                        : '';
                    res.inb.people[i] += jQuery(`#t-res tbody tr:eq(${j}) td:eq(${i-1})`).hasClass('text-warning')
                        ? ` ${jQuery(`#t-res tbody tr:eq(${j})`).attr('title')},`
                        : '';
                    res.no.people[i] += jQuery(`#t-res tbody tr:eq(${j}) td:eq(${i-1})`).hasClass('text-danger')
                        ? ` ${jQuery(`#t-res tbody tr:eq(${j})`).attr('title')},`
                        : '';
                    });
                    res.yes.people[i] = res.yes.people[i].replace(/,$/, '').replace(/^(.+)/, '\n✓ $1');
                    res.inb.people[i] = res.inb.people[i].replace(/,$/, '').replace(/^(.+)/, '\n+ $1');
                    res.no.people[i] = res.no.people[i].replace(/,$/, '').replace(/^(.+)/, '\n− $1');
                }
                jQuery(`#addition td:eq(${i})`)
                    .attr('title', `${t}${res.yes.people[i]}${res.inb.people[i]}${res.no.people[i]}`);

                Object.keys(res).forEach((k) => {
                    k === 'no'
                    ? res[k].count.push(-jQuery(`#addition td:eq(${i})`).find('.' + k + '-count').text() || 0)
                    : res[k].count.push(+jQuery(`#addition td:eq(${i})`).find('.' + k + '-count').text() || 0)
                });
            }
        });
        // Chart display
        jQuery('#chartBtn').on('click', (event) => {
            if (document.getElementById('chart-wrap').style.display === 'none') {
                document.getElementById('chart-wrap').style.display = 'block';
                const barChartData = {
                    labels : cols,
                    datasets : [
                        {
                            label: jQuery('#chart-label-yes').text(),
                            backgroundColor: 'rgba(181, 204, 111, 0.9)',
                            borderColor: 'rgba(132, 149, 81, 1)',
                            borderWidth: 1,
                            data : res.yes.count,
                        },
                        {
                            label: jQuery('#chart-label-inb').text(),
                            backgroundColor: 'rgba(255, 205, 86, 0.5)',
                            borderColor: 'rgba(255, 205, 86, 1)',
                            borderWidth: 1,
                            data : res.inb.count,
                        },

                        {
                            label: jQuery('#chart-label-no').text(),
                            backgroundColor: 'rgba(255, 185, 176, 0.5)',
                            borderColor: 'rgba(255, 185, 176, 1)',
                            borderWidth: 1,
                            data : res.no.count,
                        },
                    ]
                };

                const ctx = document.getElementById('Chart').getContext('2d');
                window.myBar = new Chart(ctx, {
                    type: 'bar',
                    data: barChartData,
                    options: {
                        tooltips: {
                            mode: 'index',
                            intersect: false
                        },
                        responsive: true,
                        scales: {
                            xAxes: [{
                                stacked: true,
                            }],
                            yAxes: [{
                                stacked: true
                            }]
                        }
                    }
                });
            }        
    
            jQuery('html, body').animate({
                scrollTop: jQuery('#Chart').offset().top - 60
            }, 2000);
            event.preventDefault();
        });
    }
});
