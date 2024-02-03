export type PaginatedResult<T> = {
  data: T[];
  current_page: number;
  from: number;
  to: number;
  per_page: number;
  last_page: number;
  total: number;
  prev_page_url: string | null;
  next_page_url: string | null;
};
