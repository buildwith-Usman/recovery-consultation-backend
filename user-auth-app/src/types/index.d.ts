interface User {
    id: number;
    name: string;
    email: string;
    phone: string;
    password: string;
    type: 'admin' | 'patient' | 'doctor';
}

interface PatientInfo {
    user_id: number;
    looking_for: 'therapists' | 'psychiatrist';
    completed: 0 | 1;
    age: number;
}

interface DoctorInfo {
    user_id: number;
    experience: string;
    dob: string | Date;
    profession: 'therapists' | 'psychiatrist';
    degree: string;
    license_no: string;
    country: number; // Assuming country is represented by an ID
    age: number;
    approved: boolean;
    completed: 0 | 1;
}

export { User, PatientInfo, DoctorInfo };