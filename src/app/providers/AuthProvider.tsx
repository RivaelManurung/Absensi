import { AxiosError } from "axios";
import { createContext, type PropsWithChildren, useContext, useEffect, useMemo, useState } from "react";
import { authService } from "../../features/auth/services/authService";
import type { AuthUser, LoginPayload } from "../../features/auth/types";

type AuthContextValue = {
  user: AuthUser | null;
  isLoading: boolean;
  login: (payload: LoginPayload) => Promise<void>;
  logout: () => Promise<void>;
  hasRole: (role: string) => boolean;
  hasPermission: (permission: string) => boolean;
};

const AuthContext = createContext<AuthContextValue | null>(null);

export function AuthProvider({ children }: PropsWithChildren) {
  const [user, setUser] = useState<AuthUser | null>(null);
  const [isLoading, setIsLoading] = useState(true);

  useEffect(() => {
    let isMounted = true;

    const bootstrapAuth = async () => {
      try {
        const response = await authService.me();
        if (isMounted) {
          setUser(response.data);
        }
      } catch (error) {
        if (error instanceof AxiosError && error.response?.status !== 401) {
          // Keep auth state null when API is unavailable or server returns non-auth errors.
          console.error("Failed to fetch authenticated user", error);
        }
      } finally {
        if (isMounted) {
          setIsLoading(false);
        }
      }
    };

    void bootstrapAuth();

    return () => {
      isMounted = false;
    };
  }, []);

  const value = useMemo(
    () => ({
      user,
      isLoading,
      login: async (payload: LoginPayload) => {
        const response = await authService.login(payload);
        setUser(response.data);
      },
      logout: async () => {
        await authService.logout();
        setUser(null);
      },
      hasRole: (role: string) => user?.roles.includes(role) ?? false,
      hasPermission: (permission: string) => user?.permissions.includes(permission) ?? false,
    }),
    [isLoading, user],
  );

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
}

export function useAuth() {
  const context = useContext(AuthContext);

  if (!context) {
    throw new Error("useAuth must be used inside AuthProvider");
  }

  return context;
}
