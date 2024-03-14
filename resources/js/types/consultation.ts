export type Consultation = {
  id: number;
  description: string;
  student_id: number;
  consultation_category_id: string;
  consultation_date: number;
};

export type WithConsultations<T> = T & {
  consultations: Consultation[];
};
