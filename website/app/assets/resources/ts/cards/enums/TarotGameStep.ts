enum TarotGameStep {
    /// Nothing yet
    NONE = 0,

    /// After initial step, we just shown the game
    INITIATED_GAME = 1,

    /// We are showing the cards, waiting to shuffle them
    READY_TO_SHUFFLE,

    /// The cards are shuffled. We wait for the user to select the cards
    CHOOSE_CARDS,

    /// All the cards are shuffled. We are querying result data before showing a process animation
    POST_CHOOSE_CARDS,

    /// All the cards are shuffled. We are showing an animation processing the cards
    PROCESS_SELECTION,

    /// All done, we are showing the results
    SHOW_RESULTS,
}
