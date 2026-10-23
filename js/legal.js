
/* =========================================================
   NEXUS LEGAL PAGE ANIMATIONS
   Scroll reveal, active navigation, smooth section tracking
   ========================================================= */

document.addEventListener("DOMContentLoaded", () => {
    const page = document.querySelector(".privacy-page");

    if (!page) {
        return;
    }

    const reduceMotion = window.matchMedia(
        "(prefers-reduced-motion: reduce)"
    ).matches;

    const revealElements = Array.from(
        page.querySelectorAll("[data-reveal]")
    );

    const navigationLinks = Array.from(
        page.querySelectorAll(".legal-nav-link")
    );

    const sections = navigationLinks
        .map((link) => {
            const targetId = link.getAttribute("href");

            if (!targetId || !targetId.startsWith("#")) {
                return null;
            }

            return document.querySelector(targetId);
        })
        .filter(Boolean);

    /*
     * Scroll reveal:
     * Reveal elements as they enter the viewport.
     * If IntersectionObserver is unavailable, show everything.
     */
    if (!reduceMotion && "IntersectionObserver" in window) {
        page.classList.add("legal-motion-ready");

        const revealObserver = new IntersectionObserver(
            (entries, observer) => {
                entries.forEach((entry) => {
                    if (!entry.isIntersecting) {
                        return;
                    }

                    entry.target.classList.add("is-visible");
                    observer.unobserve(entry.target);
                });
            },
            {
                threshold: 0.08,
                rootMargin: "0px 0px -35px 0px"
            }
        );

        revealElements.forEach((element, index) => {
            /*
             * Small stagger between cards keeps the motion polished
             * without creating a long delay.
             */
            if (element.classList.contains("legal-card")) {
                element.style.transitionDelay =
                    `${Math.min(index % 4, 3) * 70}ms`;
            }

            revealObserver.observe(element);
        });
    } else {
        revealElements.forEach((element) => {
            element.classList.add("is-visible");
        });
    }

    /*
     * Smooth navigation for the policy sidebar.
     */
    navigationLinks.forEach((link) => {
        link.addEventListener("click", (event) => {
            const targetId = link.getAttribute("href");

            if (!targetId || !targetId.startsWith("#")) {
                return;
            }

            const target = document.querySelector(targetId);

            if (!target) {
                return;
            }

            event.preventDefault();

            target.scrollIntoView({
                behavior: reduceMotion ? "auto" : "smooth",
                block: "start"
            });

            /*
             * Update the URL fragment without reloading the page.
             */
            if (window.history && window.history.replaceState) {
                window.history.replaceState(null, "", targetId);
            }
        });
    });

    /*
     * Highlight the sidebar item for the section currently in view.
     */
    if (
        !reduceMotion &&
        "IntersectionObserver" in window &&
        sections.length > 0
    ) {
        const activeObserver = new IntersectionObserver(
            (entries) => {
                const visibleEntries = entries
                    .filter((entry) => entry.isIntersecting)
                    .sort(
                        (a, b) =>
                            a.boundingClientRect.top -
                            b.boundingClientRect.top
                    );

                if (!visibleEntries.length) {
                    return;
                }

                const activeId = visibleEntries[0].target.id;

                navigationLinks.forEach((link) => {
                    const isActive =
                        link.getAttribute("href") === `#${activeId}`;

                    link.classList.toggle("is-active", isActive);

                    if (isActive) {
                        link.setAttribute("aria-current", "location");
                    } else {
                        link.removeAttribute("aria-current");
                    }
                });
            },
            {
                rootMargin: "-15% 0px -65% 0px",
                threshold: 0
            }
        );

        sections.forEach((section) => {
            activeObserver.observe(section);
        });
    }

    /*
     * Remove temporary card delays after the entrance transition.
     */
    window.addEventListener(
        "pageshow",
        () => {
            if (reduceMotion) {
                revealElements.forEach((element) => {
                    element.style.transitionDelay = "0ms";
                });
            }
        },
        { once: true }
    );
});
