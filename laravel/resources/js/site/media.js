import { qs } from './runtime.js';

export function initMediaControls(context) {
    const video = qs('#delos-video');
    const overlay = qs('#video-overlay');
    if (!video || !overlay) {
        return;
    }

    const playVideo = async () => {
        video.muted = false;

        try {
            await video.play();
            overlay.style.opacity = '0';
            overlay.style.pointerEvents = 'none';
            video.setAttribute('controls', '');
        } catch {
            video.muted = true;
        }
    };

    const pauseVideo = () => {
        video.pause();
        video.muted = true;
        overlay.style.opacity = '1';
        overlay.style.pointerEvents = 'auto';
        video.removeAttribute('controls');
    };

    overlay.addEventListener('click', () => {
        if (context.prefersReducedMotion) {
            playVideo();
            return;
        }

        playVideo();
    });

    video.addEventListener('ended', pauseVideo);
}
