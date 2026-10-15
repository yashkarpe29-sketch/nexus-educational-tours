/* =========================================================
   NEXUS EDUCATIONAL TOURS AND TRAVELS
   Main JavaScript
   ========================================================= */


/* =========================================================
   DOM READY
   ========================================================= */

document.addEventListener("DOMContentLoaded", () => {


    /* =====================================================
       MOBILE NAVIGATION
       ===================================================== */

    const navbarToggle =
        document.getElementById("navbar-toggle");

    const navbarMenu =
        document.getElementById("navbar-menu");


    if (navbarToggle && navbarMenu) {


        /* -------------------------------------------------
           Toggle Mobile Menu
           ------------------------------------------------- */

        navbarToggle.addEventListener("click", () => {

            const isOpen =
                navbarMenu.classList.toggle("is-open");

            navbarToggle.classList.toggle(
                "is-active",
                isOpen
            );

            navbarToggle.setAttribute(
                "aria-expanded",
                String(isOpen)
            );

            navbarToggle.setAttribute(
                "aria-label",
                isOpen
                    ? "Close navigation menu"
                    : "Open navigation menu"
            );

        });


        /* -------------------------------------------------
           Close Menu After Navigation Link Click
           ------------------------------------------------- */

        const navLinks =
            navbarMenu.querySelectorAll(".nav-link");


        navLinks.forEach((link) => {

            link.addEventListener("click", () => {

                navbarMenu.classList.remove("is-open");

                navbarToggle.classList.remove(
                    "is-active"
                );

                navbarToggle.setAttribute(
                    "aria-expanded",
                    "false"
                );

                navbarToggle.setAttribute(
                    "aria-label",
                    "Open navigation menu"
                );

            });

        });


        /* -------------------------------------------------
           Close Menu With Escape Key
           ------------------------------------------------- */

        document.addEventListener(
            "keydown",
            (event) => {

                if (event.key !== "Escape") {
                    return;
                }

                navbarMenu.classList.remove(
                    "is-open"
                );

                navbarToggle.classList.remove(
                    "is-active"
                );

                navbarToggle.setAttribute(
                    "aria-expanded",
                    "false"
                );

                navbarToggle.setAttribute(
                    "aria-label",
                    "Open navigation menu"
                );

                navbarToggle.focus();

            }
        );


        /* -------------------------------------------------
           Close Menu When Switching To Desktop
           ------------------------------------------------- */

        window.addEventListener("resize", () => {

            if (window.innerWidth > 900) {

                navbarMenu.classList.remove(
                    "is-open"
                );

                navbarToggle.classList.remove(
                    "is-active"
                );

                navbarToggle.setAttribute(
                    "aria-expanded",
                    "false"
                );

                navbarToggle.setAttribute(
                    "aria-label",
                    "Open navigation menu"
                );

            }

        });

    }


    /* =====================================================
       SCROLL REVEAL SYSTEM
       ===================================================== */

    const revealElements =
        document.querySelectorAll(
            `
            .section-header,
            .about-content,
            .about-visual,
            .tour-category-card,
            .featured-tour-card,
            .destination-card,
            .service-card,
            .why-nexus-card,
            .how-step,
            .enquiry-content,
            .enquiry-card,
            .gallery-feature,
            .gallery-item,
            .gallery-bottom,
            .testimonial-feature,
            .testimonial-card,
            .faq-intro,
            .faq-list,
            .final-cta-content,
            .final-cta-visual,
            .nexus-contact-heading,
            .contact-hub,
            .sp-reveal,
            .footer-brand,
            .footer-column
            `
        );


    if (!revealElements.length) {
        return;
    }


    /* -----------------------------------------------------
       Add Reveal Class
       ----------------------------------------------------- */

    revealElements.forEach((element) => {

        element.classList.add(
            "scroll-reveal"
        );

    });


    /* -----------------------------------------------------
       Reduced Motion
       ----------------------------------------------------- */

    const prefersReducedMotion =
        window.matchMedia(
            "(prefers-reduced-motion: reduce)"
        ).matches;


    if (prefersReducedMotion) {

        revealElements.forEach((element) => {

            element.classList.add(
                "is-visible"
            );

        });

        return;
    }


    /* -----------------------------------------------------
       Intersection Observer
       ----------------------------------------------------- */

    const observer =
        new IntersectionObserver(
            (entries, observerInstance) => {

                entries.forEach((entry) => {

                    if (!entry.isIntersecting) {
                        return;
                    }


                    entry.target.classList.add(
                        "is-visible"
                    );


                    observerInstance.unobserve(
                        entry.target
                    );

                });

            },
            {
                threshold: 0.12,

                rootMargin:
                    "0px 0px -60px 0px"
            }
        );


    /* -----------------------------------------------------
       Observe Elements
       ----------------------------------------------------- */

    revealElements.forEach((element) => {

        observer.observe(element);

    });

});