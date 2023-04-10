function getDocWidth()
{

    return Math.min(
            document.body.scrollWidth,
            document.documentElement.scrollWidth,
            document.body.offsetWidth,
            document.documentElement.offsetWidth,
            document.documentElement.clientWidth
            );
}


function getDocHeight()
{
    return Math.min(
            document.body.scrollHeight,
            document.documentElement.scrollHeight,
            document.body.offsetHeight,
            document.documentElement.offsetHeight,
            document.documentElement.clientHeight
            );
}

function getScrollbarWidth()
{

    // Creating invisible container
    const outer = document.createElement('div');
    outer.style.visibility = 'hidden';
    outer.style.overflow = 'scroll'; // forcing scrollbar to appear
    outer.style.msOverflowStyle = 'scrollbar'; // needed for WinJS apps
    document.body.appendChild(outer);

    // Creating inner element and placing it in the container
    const inner = document.createElement('div');
    outer.appendChild(inner);

    // Calculating difference between container's full width and the child width
    const scrollbarWidth = (outer.offsetWidth - inner.offsetWidth);

    // Removing temporary elements from the DOM
    outer.parentNode.removeChild(outer);

    return scrollbarWidth;

}
/*
function popup_confirmation(txt, ok_fnt, ok_fnt_arg, ko_fnt)
{
    console.log("popup_confirmation", txt);
    $("#popup_confirmation .text").text(txt)
    $("#popup_confirmation").removeClass("invisible")
    $("#popup_confirmation").leanModal({mode: "auto",
        closeButton: ".btn_modal_close, #btn_confirmation_ok, #btn_confirmation_ko"
    });
    $("#modal-confirmation").modal () 
    if (defined(ok_fnt))
    {
//        $("#btn_confirmation_ok").removeClass("invisible")
        $("#btn_confirmation_ok").css("display", "inline-block");

        $("#btn_confirmation_ok").click(function ()
        {
            if (defined(ok_fnt_arg))
                ok_fnt(ok_fnt_arg);
            else
                ok_fnt(ok_fnt_arg);

            $('#btn_confirmation_ok').unbind('click');
        });
    } else
    {
        $("#btn_confirmation_ok").css("display", "none");

    }

    if (defined(ko_fnt))
    {
//        $("#btn_confirmation_ko").removeClass("invisible")
        $("#btn_confirmation_ko").css("display", "inline-block");
        $("#btn_confirmation_ko").click(function ()
        {
            ko_fnt()
        });
    } else
    {
//          $("#btn_confirmation_ko").addClass("invisible")  
        $("#btn_confirmation_ko").css("display", "none");
    }
}
*/
function device_type()
{

    const userAgent = navigator.userAgent.toLowerCase();

    var isMobile = /iPhone|Android/i.test(navigator.userAgent);
    //console.log(isMobile);

    const isTablet =
            /(ipad|tablet|(android(?!.*mobile))|(windows(?!.*phone)(.*touch))|kindle|playbook|silk|(puffin(?!.*(IP|AP|WP))))/.test(
            userAgent);
    //    console.log(isTablet)

    if (isMobile)
    {
        console.log("mobile")
        return "mobile"
    } else if (isTablet)
    {
        console.log("tablet")
        return "tablet"
    } else
    {
        console.log("pc")
        return "pc";
    }
}



getAbsoluteHeight = function (el)
{
    // Get the DOM Node if you pass in a string
    el = (typeof el === 'string') ? document.querySelector(el) : el;

    var styles = window.getComputedStyle(el);
    var margin = parseFloat(styles['marginTop']) +
            parseFloat(styles['marginBottom']);

    return Math.ceil(el.offsetHeight + margin);
}

Math.easeInOutQuad = function (t, b, c, d) {
	t /= d/2;
	if (t < 1) return c/2*t*t + b;
	t--;
	return -c/2 * (t*(t-2) - 1) + b;
};

/*
scrollTo = function (final, duration, cb)
{
    console.log("scrollTo", final);
    var start = window.scrollY || document.documentElement.scrollTop,
            currentTime = null;

    console.log("start", start);
    var animateScroll = function (timestamp)
    {
        // console.log("animateScroll");
        if (!currentTime)
            currentTime = timestamp;
        var progress = timestamp - currentTime;
        if (progress > duration)
            progress = duration;
        var val = Math.easeInOutQuad(progress, start, final - start, duration);
        window.scrollTo(0, val);
        // console.log("progress",progress,"duration", duration);
        if (progress < duration)
        {
            window.requestAnimationFrame(animateScroll);
        } else
        {
            cb && cb();
        }
    };

    window.requestAnimationFrame(animateScroll);
};
*/

/*   */
function height_fit_content(fieldId)
{
    let elmt = document.getElementById(fieldId);
    //  console.log("fieldId", fieldId)
    //  console.log("elmt", elmt);
    //	console.log("scrollHeight",elmt.scrollHeight);

    elmt.style.height = "1px";
    ;
    elmt.style.height = elmt.scrollHeight + 'px';
}



function input_width_fit_content(elmt)
{
    $(elmt)
            // event handler
            .keyup(resizeInput)
            // resize on page load
            .each(resizeInput);

    function resizeInput()
    {
        //  $(this).attr('width', $(this).val().length+"ch");
        this.style.width = this.value.length + "ch";
        console.log("resizeInput", this.value, this.value.length);
    }
}
