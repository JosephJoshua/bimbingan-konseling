import { Gender } from './gender';
import { Religion } from './religion';
import { StatusInFamily } from './status-in-family';

export type Student = {
  id: number;
  nisn: string;
  nis: string;
  nik: string;
  gender: Gender;
  religion: Religion;
  full_name: string;
  birth_date: number;
  birth_place: string;
  status_in_family: StatusInFamily;
  child_order: number;
  full_address: string;
  origin_school: string;
  phone_number: string;
  email?: string;
  father_name?: string;
  father_phone_number?: string;
  mother_name?: string;
  mother_phone_number?: string;
  guardian_name?: string;
  guardian_phone_number?: string;
};

export type WithStudent<T> = T & {
  student: Student;
};
