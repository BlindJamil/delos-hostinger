export function initViewportHeight(context) {
    let resizeFrame = null;

    const setRealViewportHeight = () => {
        context.documentElement.style.setProperty('--vh', `${window.innerHeight}px`);
    };

    const handleResize = () => {
        if (resizeFrame) {
            cancelAnimationFrame(resizeFrame);
        }

        resizeFrame = requestAnimationFrame(() => {
            setRealViewportHeight();
            resizeFrame = null;
        });
    };

    setRealViewportHeight();
    window.addEventListener('resize', handleResize, { passive: true });
}
