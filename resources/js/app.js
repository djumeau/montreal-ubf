import Alpine from "alpinejs";
import gsap from "gsap";
import { SplitText } from "gsap/SplitText";

gsap.registerPlugin(SplitText);

window.Alpine = Alpine;
window.gsap = gsap;

// Home page hero (see the hero component): crossfades the slides uploaded on Manage Home Page, each one shown for a few seconds.
// The pills show the current slide and jump to another. "count" is the number of slides; the slide layers are the children of x-ref="slides"
Alpine.data("heroCarousel", (count) => ({
    slide: 0,
    loaded: false, // Only the first image comes with the page: the others are fetched once this hero is on screen
    timer: null,
    delay: 6000,
    reducedMotion: window.matchMedia("(prefers-reduced-motion: reduce)").matches,

    init() {
        if (count < 2) return;

        if (this.visible()) this.load();

        // No automatic movement for visitors who asked for less motion: the pills still change the slide
        if (!this.reducedMotion) this.play();
    },

    destroy() {
        clearInterval(this.timer);
    },

    // The page holds a mobile and a desktop hero; only the one of the current screen size is displayed
    visible() {
        return this.$el.offsetParent !== null;
    },

    load() {
        [...this.$refs.slides.children].forEach((layer) => {
            if (layer.dataset.image) {
                layer.style.backgroundImage = `url('${layer.dataset.image}')`;
                delete layer.dataset.image;
            }
        });
        this.loaded = true;
    },

    play() {
        clearInterval(this.timer);
        this.timer = setInterval(() => {
            if (!this.visible()) return;

            // Shown after a resize: this turn fetches the images, the next one moves on
            if (!this.loaded) return this.load();

            this.show((this.slide + 1) % count);
        }, this.delay);
    },

    // A pill was clicked: the slide stays its full time before the carousel moves on
    go(index) {
        this.load();
        this.show(index);
        if (!this.reducedMotion) this.play();
    },

    show(index) {
        if (index === this.slide) return;

        const layers = [...this.$refs.slides.children];
        const duration = this.reducedMotion ? 0 : 1.4;

        // Crossfade, the new image settling from slightly closer. Every other layer fades out,
        // so one still fading in from a quick click on another pill doesn't stay half shown
        gsap.to(layers.filter((layer, position) => position !== index), { opacity: 0, duration, ease: "power2.inOut", overwrite: true });
        gsap.fromTo(
            layers[index],
            { opacity: 0, scale: this.reducedMotion ? 1 : 1.06 },
            { opacity: 1, scale: 1, duration, ease: "power2.inOut", overwrite: true },
        );

        this.slide = index;
    },
}));

Alpine.start();

// Home page hero, when the page loads: the dark overlay fades in over 2 seconds and the heading appears letter by letter.
// Both start hidden in the hero component (data-hero-overlay, data-hero-title)
function animateHero() {
    const reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

    document.querySelectorAll("[data-hero]").forEach((hero) => {
        const overlay = hero.querySelector("[data-hero-overlay]");
        const title = hero.querySelector("[data-hero-title]");

        // Visitors who asked for less motion get both right away
        if (reducedMotion) {
            gsap.set(overlay, { opacity: 1 });
            gsap.set(title, { visibility: "visible" });
            return;
        }

        gsap.to(overlay, { opacity: 1, duration: 2, ease: "power1.out" });

        // Words stay whole when the heading wraps; screen readers still get the sentence (SplitText labels the heading)
        const split = SplitText.create(title, { type: "words,chars" });

        gsap.set(title, { visibility: "visible" });
        gsap.from(split.chars, {
            opacity: 0,
            y: 24,
            duration: 0.6,
            ease: "power3.out",
            stagger: { amount: 1.4 }, // First to last letter, whatever the length of the heading
            delay: 0.3,
        });
    });
}

animateHero();

document.querySelector("#hamburger").addEventListener("click", function () {
    const menu = document.querySelector("#mobile-menu");
    menu.classList.toggle("hidden");
});
