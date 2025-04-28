// assets/js/reloadOnBack.js

export function reloadOnBack() {
    if (performance.getEntriesByType("navigation")[0].type === "back_forward") {
        window.location.reload();
    }
}