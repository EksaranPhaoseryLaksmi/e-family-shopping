const express = require("express");
const bodyParser = require("body-parser");
const axios = require("axios"); // Added for API calls
const {
  BakongKHQR,
  IndividualInfo,
  khqrData
} = require("bakong-khqr");

const app = express();
app.use(bodyParser.json());

// --- CONFIGURATION ---
const BAKONG_API_TOKEN = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJkYXRhIjp7ImlkIjoiNTY3N2RiNzAzNzA1NDUzMyJ9LCJpYXQiOjE3NzU5Mjc5NDQsImV4cCI6MTc4MzcwMzk0NH0.uSLBpqgTcu4P6iiNdA0_7n7H5t3VPjIRTIPA1oBZ6Jw"; // Get this from Bakong dev portal
const BAKONG_CHECK_URL = "https://api-bakong.nbc.org.kh/v1/check_transaction_by_md5";

/**
 * 1. Generate KHQR
 */
app.post("/generate-khqr", (req, res) => {
  try {
    const data = req.body;
    const expiration = Date.now() + 5 * 60 * 1000;

    const optionalData = {
      currency: khqrData.currency.khr,
      amount: data.amount || undefined,
      merchantCategoryCode: "5999",
      mobileNumber: data.mobileNumber || "",
      storeLabel: data.storeLabel || "",
      terminalLabel: data.terminalLabel || "",
      purposeOfTransaction: data.purpose || "",
      expirationTimestamp: expiration
    };

    const individualInfo = new IndividualInfo(
      data.bakongAccountID,
      data.merchantName,
      data.merchantCity,
      optionalData
    );

    const KHQR = new BakongKHQR();
    const result = KHQR.generateIndividual(individualInfo);

    if (result.status.code !== 0) {
      return res.status(400).json({ success: false, error: "KHQR generation failed" });
    }

    return res.json({
      success: true,
      qrString: result.data.qr,
      md5: result.data.md5, // Use this as your payment_ref
      expiration: expiration
    });

  } catch (error) {
    return res.status(500).json({ success: false, error: error.message });
  }
});

/**
 * 2. Check Payment Status (Bakong Real Open API)
 */

app.post("/check-payment", async (req, res) => {

    const md5 = req.body.md5;

    if (!md5) {
        return res.status(400).json({
            success: false,
            error: "Missing md5"
        });
    }

    try {

        const response = await axios.post(
            "https://api-bakong.nbc.gov.kh/v1/check_transaction_by_md5",
            { md5 },
            {
                headers: {
                    Authorization: "Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJkYXRhIjp7ImlkIjoiNTY3N2RiNzAzNzA1NDUzMyJ9LCJpYXQiOjE3NzU5Mjc5NDQsImV4cCI6MTc4MzcwMzk0NH0.uSLBpqgTcu4P6iiNdA0_7n7H5t3VPjIRTIPA1oBZ6Jw",
                    "Content-Type": "application/json"
                }
            }
        );

        return res.json({
            success: true,
            data: response.data
        });

    } catch (err) {
        return res.status(500).json({
            success: false,
            error: err.response?.data || err.message
        });
    }
});

app.listen(3000, () => {
  console.log("KHQR SDK & Checker server running on port 3000");
});
