
document.addEventListener("DOMContentLoaded", () => {
  const enquiryForm = document.getElementById("enquiry-form");

  if (!enquiryForm) return;

  // Replace this with your verified business WhatsApp number.
  // Use country code 91, without + or spaces.
  const whatsappNumber = "917448228001";

  enquiryForm.addEventListener("submit", (event) => {
    event.preventDefault();

    const name = document.getElementById("enquiry-name").value.trim();
    const institution = document
      .getElementById("enquiry-institution")
      .value.trim();
    const phone = document.getElementById("enquiry-phone").value.trim();
    const group = document.getElementById("enquiry-group").value.trim();
    const tour = document.getElementById("enquiry-tour").value;
    const message = document
      .getElementById("enquiry-message")
      .value.trim();

    // Basic validation
    if (!name) {
      alert("Please enter your name.");
      document.getElementById("enquiry-name").focus();
      return;
    }

    const phoneDigits = phone.replace(/\D/g, "");

    if (phoneDigits.length < 10 || phoneDigits.length > 15) {
      alert("Please enter a valid mobile number.");
      document.getElementById("enquiry-phone").focus();
      return;
    }

    if (group && (!Number.isInteger(Number(group)) || Number(group) < 1)) {
      alert("Please enter a valid group size.");
      document.getElementById("enquiry-group").focus();
      return;
    }

    const whatsappMessage = [
      "Hello Nexus Educational Tours and Travels,",
      "",
      "I would like to enquire about a tour.",
      "",
      `Name: ${name}`,
      `Institution: ${institution || "Not provided"}`,
      `Mobile Number: ${phone}`,
      `Group Size: ${group || "Not specified"}`,
      `Tour Type: ${tour || "Not selected"}`,
      "",
      "Requirements:",
      message || "No additional requirements provided.",
      "",
      "Please contact me with more details.",
    ].join("\n");

    const whatsappURL =
      `https://wa.me/${whatsappNumber}?text=` +
      encodeURIComponent(whatsappMessage);

    // Open WhatsApp with the enquiry message pre-filled.
    window.open(whatsappURL, "_blank", "noopener,noreferrer");
  });
});