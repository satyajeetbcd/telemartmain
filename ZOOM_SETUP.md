# Zoom SDK Integration Setup Guide

This application integrates Zoom video meetings for appointments. When an appointment is confirmed, a Zoom meeting is automatically created.

## Prerequisites

1. A Zoom account (Pro, Business, or Enterprise plan recommended)
2. Zoom App credentials (Server-to-Server OAuth)

## Setup Instructions

### 1. Create Zoom App

1. Go to [Zoom App Marketplace](https://marketplace.zoom.us/)
2. Sign in with your Zoom account
3. Click "Develop" → "Build App"
4. Choose "Server-to-Server OAuth" app type
5. Fill in the app information:
   - App name: "Tele Health Mart"
   - Company name: Your company name
   - Developer contact: Your email
6. Click "Create"

### 2. Get API Credentials

After creating the app, you'll need:

1. **Account ID**: Found in the "App Credentials" section
2. **Client ID**: Found in the "App Credentials" section
3. **Client Secret**: Found in the "App Credentials" section (click "Show" to reveal)

### 3. Configure App Scopes

In your Zoom app settings, enable the following scopes:
- `meeting:write` - Create and manage meetings
- `meeting:read` - Read meeting information
- `user:read` - Read user information

### 4. Add Credentials to .env

Add the following to your `.env` file:

```env
ZOOM_ACCOUNT_ID=your_account_id_here
ZOOM_CLIENT_ID=your_client_id_here
ZOOM_CLIENT_SECRET=your_client_secret_here
```

### 5. Clear Config Cache

After adding credentials, clear the config cache:

```bash
php artisan config:clear
```

## How It Works

1. **Appointment Booking**: When a patient books an appointment, it's created with status "pending"
2. **Appointment Confirmation**: When a doctor confirms the appointment (changes status to "confirmed"), a Zoom meeting is automatically created
3. **Meeting Details**: The Zoom meeting details (join URL, password, etc.) are stored with the appointment
4. **Access**: Both doctor and patient can access the Zoom meeting link from the appointment details page
5. **Cancellation**: If an appointment is cancelled, the Zoom meeting is automatically deleted

## Features

- ✅ Automatic Zoom meeting creation on appointment confirmation
- ✅ Meeting details stored with appointment
- ✅ Join links accessible to both doctor and patient
- ✅ Automatic meeting deletion on cancellation
- ✅ Meeting password included for security

## Testing

1. Create a test appointment
2. As a doctor, confirm the appointment
3. Check the appointment details page - you should see a "Join Zoom Meeting" button
4. Click the button to verify the Zoom meeting link works

## Troubleshooting

### Meeting Not Created

- Check that Zoom credentials are correctly set in `.env`
- Verify the credentials are valid in Zoom App Marketplace
- Check Laravel logs for error messages
- Ensure the app has the required scopes enabled

### Authentication Errors

- Verify `ZOOM_ACCOUNT_ID`, `ZOOM_CLIENT_ID`, and `ZOOM_CLIENT_SECRET` are correct
- Make sure there are no extra spaces in the `.env` file
- Clear config cache: `php artisan config:clear`

### Meeting Creation Fails

- Check Zoom API rate limits
- Verify your Zoom account has meeting creation permissions
- Check network connectivity to Zoom API

## API Documentation

For more information, refer to:
- [Zoom API Documentation](https://marketplace.zoom.us/docs/api-reference/zoom-api/)
- [Server-to-Server OAuth](https://marketplace.zoom.us/docs/guides/build/server-to-server-oauth-app/)

