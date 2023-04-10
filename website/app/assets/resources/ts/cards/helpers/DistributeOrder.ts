/**
 * @brief A helper with miscellaneous methods to define card distribution order.
 */
class DistributeOrder {

    /**
     * @brief Computes distribution delay so that the cards are distributed one after another.
     * @param number baseDelay  Base distribution delay in milliseconds.
     * @param number i          The index of the card to compute the delay for.
     * @param number count      How many cards there are.
     * @return number The computed delay.
     */
    public static orderedDistribution(baseDelay: number, i: number, count: number) {
        return i * baseDelay;
    }

    /**
     * @brief Computes distribution delay so that the first and last card are distributed first and central cards last.
     * @param number baseDelay  Base distribution delay in milliseconds.
     * @param number i          The index of the card to compute the delay for.
     * @param number count      How many cards there are.
     * @return number The computed delay.
     */
    public static outFirstDistribution(baseDelay: number, i: number, count: number) {
        return Math.abs(i - (i > count / 2 ? count - 1 : 0)) * 2 * baseDelay;
    }

    /**
     * @brief Computes distribution delay so that all the cards are distributed at the same time.
     * @param number baseDelay  Base distribution delay in milliseconds.
     * @param number i          The index of the card to compute the delay for.
     * @param number count      How many cards there are.
     * @return number The computed delay.
     */
    public static synchroniousDistribution(baseDelay: number, i: number, count: number) {
        return 0;
    }
}
