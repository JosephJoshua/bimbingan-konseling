export type ConsultationCategory = {
  id: number;
  name: string;
};

export type WithConsultationCategory<T> = T & {
  consultation_category: ConsultationCategory;
};
