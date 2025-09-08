const mongoose = require('mongoose');

const doctorInfoSchema = new mongoose.Schema({
    user_id: {
        type: mongoose.Schema.Types.ObjectId,
        ref: 'User',
        required: true
    },
    experience: {
        type: String,
        required: true
    },
    dob: {
        type: Date,
        required: true
    },
    profession: {
        type: String,
        enum: ['therapists', 'psychiatrist'],
        required: true
    },
    degree: {
        type: String,
        required: true
    },
    license_no: {
        type: String,
        required: true
    },
    country: {
        type: mongoose.Schema.Types.ObjectId,
        ref: 'Country', // Assuming you have a Country model
        required: true
    },
    age: {
        type: Number,
        required: true
    },
    approved: {
        type: Boolean,
        default: false
    },
    completed: {
        type: Number,
        default: 0
    }
});

module.exports = mongoose.model('DoctorInfo', doctorInfoSchema);