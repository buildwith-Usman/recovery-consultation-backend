# User Authentication Application

This project is a user authentication application built with Node.js and Express. It provides functionality for user registration and login, utilizing Passport.js for authentication.

## Project Structure

```
user-auth-app
├── src
│   ├── app.js                # Entry point of the application
│   ├── config
│   │   └── passport.js       # Passport.js configuration for authentication
│   ├── controllers
│   │   └── authController.js  # Controller for handling authentication logic
│   ├── models
│   │   ├── doctorInfo.js      # Model for doctor information
│   │   ├── patientInfo.js      # Model for patient information
│   │   └── user.js            # User model schema
│   ├── routes
│   │   └── auth.js            # Routes for authentication
│   └── types
│       └── index.d.ts         # TypeScript types and interfaces
├── package.json               # NPM configuration file
├── .env                       # Environment variables
└── README.md                  # Project documentation
```

## Installation

1. Clone the repository:
   ```
   git clone <repository-url>
   ```

2. Navigate to the project directory:
   ```
   cd user-auth-app
   ```

3. Install the dependencies:
   ```
   npm install
   ```

4. Create a `.env` file in the root directory and add your environment variables, such as database connection strings and secret keys.

## Usage

1. Start the application:
   ```
   npm start
   ```

2. The application will run on `http://localhost:3000` (or the port specified in your configuration).

## API Endpoints

- **POST /register**: Register a new user.
- **POST /login**: Authenticate an existing user.

## Contributing

Contributions are welcome! Please open an issue or submit a pull request for any improvements or bug fixes.

## License

This project is licensed under the MIT License.