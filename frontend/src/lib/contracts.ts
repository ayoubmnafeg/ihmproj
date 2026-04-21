export type ApiProfile = {
  display_name: string | null;
};

export type ApiUser = {
  id: string;
  email: string;
  status: string;
  profile?: ApiProfile | null;
};

export type AuthSession = {
  token: string;
  user: ApiUser;
};

export type AuthResponse = AuthSession;

export type Publication = {
  id: string;
  title: string;
  text: string;
  author_id: string;
  created_at: string;
  updated_at: string;
  media_type?: string | null;
  author?: ApiUser | null;
};

export type PaginatedResponse<T> = {
  current_page: number;
  data: T[];
  last_page: number;
  per_page: number;
  total: number;
};
