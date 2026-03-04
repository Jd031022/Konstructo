// get-refresh-token.js
import { google } from 'googleapis';
import readline from 'readline';
import dotenv from 'dotenv';

dotenv.config();

const oauth2Client = new google.auth.OAuth2(
  process.env.GOOGLE_CLIENT_ID,
  process.env.GOOGLE_CLIENT_SECRET,
  'https://developers.google.com/oauthplayground' // EXACT match
);

const SCOPES = ['https://www.googleapis.com/auth/gmail.send'];

const authUrl = oauth2Client.generateAuthUrl({
  access_type: 'offline',
  scope: SCOPES,
  prompt: 'consent',
  include_granted_scopes: true
});

console.log('\n🔗 Click this URL to authorize:\n', authUrl);
console.log('\n📝 After authorizing, you will be redirected to the OAuth Playground');
console.log('🔑 Look for the "code" parameter in the URL and paste it below\n');

const rl = readline.createInterface({
  input: process.stdin,
  output: process.stdout,
});

rl.question('✏️  Enter the code from the URL: ', async (code) => {
  rl.close();
  
  try {
    const { tokens } = await oauth2Client.getToken(code);
    console.log('\n✅ SUCCESS! Here are your tokens:\n');
    console.log('🔄 Refresh Token:', tokens.refresh_token);
    console.log('🔑 Access Token:', tokens.access_token);
    console.log('⏰ Expires:', new Date(tokens.expiry_date).toLocaleString());
    
    console.log('\n📌 Add this to your .env file:');
    console.log(`GOOGLE_REFRESH_TOKEN=${tokens.refresh_token}`);
  } catch (error) {
    console.error('\n❌ Error getting tokens:', error.message);
  }
});