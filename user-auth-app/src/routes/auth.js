import express from 'express';
import { registerUser, loginUser } from '../controllers/authController';
import passport from 'passport';

const router = express.Router();

router.post('/register', registerUser);
router.post('/login', passport.authenticate('local', { session: false }), loginUser);

export default router;