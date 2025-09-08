const mongoose = require('mongoose');

const patientInfoSchema = new mongoose.Schema({
    user_id: {
        type: mongoose.Schema.Types.ObjectId,
        ref: 'User',
        required: true
    },
    looking_for: {
        type: String,
        enum: ['therapists', 'psychiatrist'],
        required: true
    },
    completed: {
        type: Number,
        enum: [0, 1],
        default: 0
    },
    age: {
        type: Number,
        required: true
    }
}, { timestamps: true });

module.exports = mongoose.model('PatientInfo', patientInfoSchema);