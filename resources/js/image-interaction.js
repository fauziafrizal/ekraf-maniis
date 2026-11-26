// Prevent native image dragging, including photos added by scripts.
// Do not cancel pointer events, gallery clicks, carousel scrolling or text selection.
function protectImages(root) {
    if (root instanceof HTMLImageElement) root.draggable = false;
    root.querySelectorAll?.('img').forEach((image) => {
        image.draggable = false;
    });
}

protectImages(document);

document.addEventListener(
    'dragstart',
    (event) => {
        const target = event.target;
        if (!(target instanceof Element)) return;
        const imageLink = target.closest('a');
        if (target.closest('img') || imageLink?.querySelector('img')) {
            event.preventDefault();
        }
    },
    true
);

const observer = new MutationObserver((records) => {
    for (const record of records) {
        for (const node of record.addedNodes) {
            if (node instanceof Element) protectImages(node);
        }
    }
});
observer.observe(document.body, { childList: true, subtree: true });
