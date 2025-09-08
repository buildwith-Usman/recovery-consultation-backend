const passport = require('passport');
const LocalStrategy = require('passport-local').Strategy;
const User = require('../models/user');

// Serialize user
passport.serializeUser((user, done) => {
    done(null, user.id);
});

// Deserialize user
passport.deserializeUser((id, done) => {
    User.findById(id, (err, user) => {
        done(err, user);
    });
});

// Local strategy for username and password login
passport.use('local-login', new LocalStrategy({
    usernameField: 'email',
    passwordField: 'password',
    passReqToCallback: true
}, (req, email, password, done) => {
    User.findOne({ email: email }, (err, user) => {
        if (err) return done(err);
        if (!user) return done(null, false, req.flash('loginMessage', 'No user found.'));
        if (!user.validPassword(password)) return done(null, false, req.flash('loginMessage', 'Wrong password.'));
        return done(null, user);
    });
}));

// Local strategy for user registration
passport.use('local-signup', new LocalStrategy({
    usernameField: 'email',
    passwordField: 'password',
    passReqToCallback: true
}, (req, email, password, done) => {
    User.findOne({ email: email }, (err, user) => {
        if (err) return done(err);
        if (user) return done(null, false, req.flash('signupMessage', 'That email is already taken.'));
        
        const newUser = new User();
        newUser.name = req.body.name;
        newUser.email = email;
        newUser.password = newUser.generateHash(password);
        newUser.type = req.body.type;

        newUser.save((err) => {
            if (err) throw err;
            return done(null, newUser);
        });
    });
}));

module.exports = passport;