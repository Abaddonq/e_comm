export function registerGlobals(map) {
    Object.entries(map).forEach(([key, value]) => {
        window[key] = value;
    });
}
