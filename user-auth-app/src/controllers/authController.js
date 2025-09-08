const User = require('../models/user');
const bcrypt = require('bcrypt');
const passport = require('passport');

// User Registration
exports.register = async (req, res) => {
    const { name, email, phone, password, type } = req.body;

    try {
        // Check if user already exists
        let existingUser = await User.findOne({ email });
        if (existingUser) {
            return res.status(400).json({ message: 'User already exists' });
        }

        // Hash the password
        const hashedPassword = await bcrypt.hash(password, 10);

        // Create new user
        const newUser = new User({
            name,
            email,
            phone,
            password: hashedPassword,
            type
        });

        await newUser.save();
        res.status(201).json({ message: 'User registered successfully' });
    } catch (error) {
        res.status(500).json({ message: 'Server error', error });
    }
};

// User Login
exports.login = (req, res, next) => {
    passport.authenticate('local', (err, user, info) => {
        if (err) {
            return res.status(500).json({ message: 'Server error', error: err });
        }
        if (!user) {
            return res.status(400).json({ message: 'Invalid credentials' });
        }
        req.logIn(user, (err) => {
            if (err) {
                return res.status(500).json({ message: 'Server error', error: err });
            }
            res.status(200).json({ message: 'Login successful', user });
        });
    })(req, res, next);
};