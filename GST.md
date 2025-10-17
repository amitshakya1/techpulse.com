# GST e-Invoice & E-Way Bill Integration Guide

This document provides a step-by-step guide for developers to integrate **GST e-Invoice** and **E-Way Bill** APIs in a Laravel project.

---

## 1. Confirm Eligibility

Before integrating, ensure your business is **eligible for e-Invoicing / e-Way Bill**:

* Check turnover thresholds and notifications issued by the GST authorities.
* Reference: [ClearTax GST e-Invoice Eligibility](https://docs.cleartax.in/gst/e-invoice)

---

## 2. Register & Enable Portals

You need to register your GSTIN and enable API access on the official portals:

### a) E-Invoice Portal

* Production: [https://einvoice1.gst.gov.in](https://einvoice1.gst.gov.in)
* Sandbox (for testing): [https://einv-apisandbox.nic.in](https://einv-apisandbox.nic.in)

### b) E-Way Bill Portal

* Production & developer docs: [https://ewaybillgst.gov.in](https://ewaybillgst.gov.in)
* Developer documentation: [https://docs.ewaybillgst.gov.in](https://docs.ewaybillgst.gov.in)

---

## 3. Create API User Credentials

* On the portal, create **API user credentials** (username/password).
* For **direct access**, you may also need `client_id` and `client_secret`.
* OTP confirmation is required, and you can select **Direct** or **GSP** mode.
* Reference: [Developer Sandbox](https://developer.sandbox.co.in)

---

## 4. Whitelist Your IP

* NIC portals only allow API requests from **whitelisted public IP addresses**.
* Make sure your server’s public IP is added to the whitelist.
* Reference: [ClearTax GST API Integration](https://docs.cleartax.in/gst/e-invoice)

---

## 5. Use Sandbox First

* Always start with the **sandbox environment** to test:

  * JSON payload schema
  * IRN generation
  * QR code generation
  * Validation
* Sandbox reference: [eInvoice Sandbox](https://einv-apisandbox.nic.in)

---

## 6. Get Auth Token

* After creating the API user, request an **auth token**.
* The token is **time-limited** (typically a few hours) and is required for both:

  * e-Invoice API
  * E-Way Bill API
* Reference: [eInvoice API Docs](https://einv-apisandbox.nic.in)

---

## 7. Laravel Integration Example

### a) `.env` Configuration

```env
GST_HOST=https://einv-apisandbox.nic.in
GST_API_USERNAME=your_api_username
GST_API_PASSWORD=your_api_password
GST_CLIENT_ID=your_client_id
GST_CLIENT_SECRET=your_client_secret
GSTIN=27ABCDE1234F2Z5
GST_TOKEN_TTL=360
```

