/// <reference path="../Globals" />
/// <reference path="../helpers/TemplateHelper" />
/// <reference path="../TarotGame" />

/**
 * @brief Sets the html in a smooth manner.
 * @param JQuery container The container where we are going to set the text.
 * @param string html The html content to set.
 * @param int delay The animation delay.
 * @param string|boolean easing Default animation easing to be passed to jquery.
 * @param any options Extra options to pass to jquery.
 * @return Promise A promise for when the function is done.
 */
function animSetText(
    container: any,
    html: string,
    delay: number = TemplateHelper.ANIMATION_DELAY_TEXT,
    easing: string|boolean = TemplateHelper.ANIMATION_EASING_TEXT,
    options: any = {}
) {
    return new Promise<void>((resolve, reject) => {
        const oldSpan = container.find('> span').first();
        const oldHtml = (oldSpan.length ? oldSpan.html() : container.html()).trim();
        html = html.trim();

        // check if we have nothing to do
        if (oldSpan.length && oldHtml === html) {
            resolve();
            return;
        }

        // cancel any existing animation and start the new one
        TemplateHelper.cancelAnimationOnElement(container).then(() => {
            const newSpan: any = $('<span>' + html + '</span>');
            const oldSpan: any = $('<span>' + oldHtml + '</span>');

            const oldContainerPosition: string = container.css('position');
            container.css('position', 'relative');

            newSpan.css('opacity', 0);

            oldSpan.css({
                position: 'absolute',
                top: container.css('padding-top'),
                left: '50%',
                transform: 'translateX(-50%)',
                opacity: 1,
                'text-align': 'center',
                width: container.width() + 'px'
            });

            container.html('');
            container.append(newSpan);
            container.append(oldSpan);

            TemplateHelper.registerAnimationOnElement(container, function() {
                oldSpan.remove();
                newSpan.stop();
                newSpan.css({ opacity: 1 });
                if (!newSpan.html().trim()) {
                    newSpan.remove();
                }
                container.css('position', oldContainerPosition);
                resolve();
            });

            oldSpan.animate({ opacity: 0 }, {
                ...options,
                duration: delay,
                easing: easing,
                queue: false,
            });
            newSpan.animate({ opacity: 1 }, {
                ...options,
                duration: delay,
                easing: easing,
                queue: false,
                always: function() {
                    TemplateHelper.cancelAnimationOnElement(container)
                }
            });
        });
    });
}
