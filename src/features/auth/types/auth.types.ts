export type LoginPayload = {
  email: string;
  password: string;
};

export type AuthUser = {
  id: string;
  name: string;
  email: string;
  is_active: boolean;
  roles: string[];
  permissions: string[];
};

export type LoginResponse = {
  message: string;
  data: AuthUser;
};

export type MeResponse = {
  data: AuthUser;
};
