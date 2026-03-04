// test-email.mjs
import nodemailer from 'nodemailer';
import { google } from 'googleapis';
import dotenv from 'dotenv';

dotenv.config();

async function sendTestEmail() {
  const oauth2Client = new google.auth.OAuth2(
    process.env.GOOGLE_CLIENT_ID,
    process.env.GOOGLE_CLIENT_SECRET,
    process.env.GOOGLE_REDIRECT_URI
  );

  oauth2Client.setCredentials({
    refresh_token: process.env.GOOGLE_REFRESH_TOKEN
  });

  try {
    const accessToken = await oauth2Client.getAccessToken();
    
    const transporter = nodemailer.createTransport({
      service: 'gmail',
      auth: {
        type: 'OAuth2',
        user: process.env.EMAIL_USER,
        clientId: process.env.GOOGLE_CLIENT_ID,
        clientSecret: process.env.GOOGLE_CLIENT_SECRET,
        refreshToken: process.env.GOOGLE_REFRESH_TOKEN,
        accessToken: accessToken.token
      }
    });

    const info = await transporter.sendMail({
      from: `"Konstructo" <${process.env.EMAIL_USER}>`,
      to: 'jjm2022-6024-83583@bicol-u.edu.ph', // Send to yourself for testing
      subject: 'Test Email from Konstructo',
      text: 'If you receive this, everything is working!',
      html: '<h1>Success!</h1><p>Your Gmail API integration is working.</p>'
    });

    console.log('✅ Email sent successfully!', info.messageId);
  } catch (error) {
    console.error('❌ Error:', error);
  }
}

sendTestEmail();