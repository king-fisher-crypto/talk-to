<?php $id = $id ?? rand(1, 1000000) ?>
<div class="utilities">
    <fieldset class="rate" data-value="<?= $rating ?? 0 ?>">
        <input type="radio" id="<?= $id ?>_rating10" name="<?= $id ?>_rating" value="10" /><label for="<?= $id ?>_rating10" title="5 stars"></label>
        <input type="radio" id="<?= $id ?>_rating9" name="<?= $id ?>_rating" value="9" /><label class="half" for="<?= $id ?>_rating9" title="4 1/2 stars"></label>
        <input type="radio" id="<?= $id ?>_rating8" name="<?= $id ?>_rating" value="8" /><label for="<?= $id ?>_rating8" title="4 stars"></label>
        <input type="radio" id="<?= $id ?>_rating7" name="<?= $id ?>_rating" value="7" /><label class="half" for="<?= $id ?>_rating7" title="3 1/2 stars"></label>
        <input type="radio" id="<?= $id ?>_rating6" name="<?= $id ?>_rating" value="6" /><label for="<?= $id ?>_rating6" title="3 stars"></label>
        <input type="radio" id="<?= $id ?>_rating5" name="<?= $id ?>_rating" value="5" /><label class="half" for="<?= $id ?>_rating5" title="2 1/2 stars"></label>
        <input type="radio" id="<?= $id ?>_rating4" name="<?= $id ?>_rating" value="4" /><label for="<?= $id ?>_rating4" title="2 stars"></label>
        <input type="radio" id="<?= $id ?>_rating3" name="<?= $id ?>_rating" value="3" /><label class="half" for="<?= $id ?>_rating3" title="1 1/2 stars"></label>
        <input type="radio" id="<?= $id ?>_rating2" name="<?= $id ?>_rating" value="2" /><label for="<?= $id ?>_rating2" title="1 star"></label>
        <input type="radio" id="<?= $id ?>_rating1" name="<?= $id ?>_rating" value="1" /><label class="half" for="<?= $id ?>_rating1" title="1/2 star"></label>

    </fieldset>
</div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Get all rating elements on the page
        const ratings = document.querySelectorAll('.rate');

        // Loop through each rating element
        ratings.forEach(rating => {
            // Get the rating value from the data-value attribute
            const ratingValue = parseFloat(rating.getAttribute('data-value'));

            // Loop through each input element
            const inputs = rating.querySelectorAll('input');
            let checkedInput = null;
            let closestDifference = Number.MAX_VALUE;
            inputs.forEach(input => {
                // Get the value of the current input
                const inputValue = parseFloat(input.getAttribute('value'));

                // Adjust the input value to be consistent with the 5-star rating system
                const adjustedValue = ratingValue * 2;

                // Determine the difference between the adjusted value and the rating value
                const difference = Math.abs(inputValue - adjustedValue);

                if (difference < closestDifference) {
                    // If the current input is closer to the adjusted value than the current closest input, set it as the checked input
                    closestDifference = difference;
                    checkedInput = input;
                }
            });

            // Check the input that is closest to the adjusted value
            if (checkedInput) {
                checkedInput.checked = true;
            }

            // Disable all inputs after the ratings have been rendered
            inputs.forEach(input => {
                input.disabled = true;
            });
        });

    });
</script>