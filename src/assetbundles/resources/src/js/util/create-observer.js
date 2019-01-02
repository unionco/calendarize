
export const createObserver = (cb) => {
    const mutationObserver = new MutationObserver(function (mutations) {
        mutations.forEach((mutation) => {
            cb(mutation);
        });
    });

    return mutationObserver;
}