import { useMutation, useQuery, useQueryClient } from "@tanstack/react-query";
import { useEffect, useState } from "react";
import { Link, useNavigate, useParams } from "react-router-dom";
import { useToast } from "../../../app/providers/ToastProvider";
import { Button } from "../../../components/ui/button";
import { getApiErrorMessage } from "../../../lib/apiError";
import { APP_ROUTES } from "../../../lib/constants";
import { Input } from "../../../components/ui/input";
import { UserFormSection } from "../components/UserFormSection";
import { userService } from "../services/userService";

export function UsersEditPage() {
  const { id } = useParams();
  const navigate = useNavigate();
  const queryClient = useQueryClient();
  const toast = useToast();
  const [form, setForm] = useState({ name: "", email: "", password: "", isActive: true });
  const [newPassword, setNewPassword] = useState("");

  const userQuery = useQuery({
    queryKey: ["users-detail", id],
    queryFn: () => userService.getUser(id as string),
    enabled: typeof id === "string" && id.length > 0,
  });

  useEffect(() => {
    if (!userQuery.data) {
      return;
    }

    setForm((previous) => ({
      ...previous,
      name: userQuery.data?.name ?? "",
      email: userQuery.data?.email ?? "",
      password: "",
      isActive: Boolean(userQuery.data?.is_active),
    }));
  }, [userQuery.data]);

  const updateMutation = useMutation({
    mutationFn: () =>
      userService.updateUser(id as string, {
        name: form.name,
        email: form.email,
        is_active: form.isActive,
        ...(form.password.trim().length > 0 ? { password: form.password } : {}),
      }),
    onSuccess: async () => {
      await queryClient.invalidateQueries({ queryKey: ["users"] });
      await queryClient.invalidateQueries({ queryKey: ["users-detail", id] });
      setForm((previous) => ({ ...previous, password: "" }));
      toast.success("User berhasil diupdate.");
    },
    onError: (error) => {
      toast.error(getApiErrorMessage(error, "Gagal mengupdate user."));
    },
  });

  const resetPasswordMutation = useMutation({
    mutationFn: () => userService.resetPassword(id as string, newPassword),
    onSuccess: async () => {
      await queryClient.invalidateQueries({ queryKey: ["users-detail", id] });
      setNewPassword("");
      toast.success("Password user berhasil direset.");
    },
    onError: (error) => {
      toast.error(getApiErrorMessage(error, "Gagal reset password user."));
    },
  });

  const deleteMutation = useMutation({
    mutationFn: () => userService.deleteUser(id as string),
    onSuccess: async () => {
      await queryClient.invalidateQueries({ queryKey: ["users"] });
      toast.success("User berhasil dihapus.");
      navigate(APP_ROUTES.users);
    },
    onError: (error) => {
      toast.error(getApiErrorMessage(error, "Gagal menghapus user."));
    },
  });

  if (userQuery.isLoading) {
    return <p className="text-sm text-muted-foreground">Loading user...</p>;
  }

  return (
    <section className="space-y-6">
      <header>
        <h1 className="text-2xl font-semibold">Edit User</h1>
        <p className="text-sm text-muted-foreground">User ID: {id}</p>
      </header>

      <form
        className="grid gap-4 rounded-2xl border border-border bg-card p-5"
        onSubmit={(event) => {
          event.preventDefault();
          updateMutation.mutate();
        }}
      >
        <UserFormSection
          name={form.name}
          email={form.email}
          password={form.password}
          isActive={form.isActive}
          onChange={(next) => setForm((previous) => ({ ...previous, ...next }))}
        />

        <div className="space-y-2">
          <p className="text-sm text-muted-foreground">Reset Password</p>
          <div className="flex flex-wrap gap-3">
            <Input
              type="password"
              placeholder="New password"
              value={newPassword}
              onChange={(event) => setNewPassword(event.target.value)}
            />
            <Button
              type="button"
              variant="outline"
              disabled={resetPasswordMutation.isPending || newPassword.trim().length < 6}
              onClick={() => resetPasswordMutation.mutate()}
            >
              Reset Password
            </Button>
          </div>
        </div>

        <div className="flex flex-wrap gap-3">
          <Button type="submit" disabled={updateMutation.isPending}>Update User</Button>
          <Button
            type="button"
            variant="outline"
            disabled={deleteMutation.isPending}
            onClick={() => {
              if (window.confirm("Delete this user? This action cannot be undone.")) {
                deleteMutation.mutate();
              }
            }}
          >
            Delete User
          </Button>
          <Link to={APP_ROUTES.users}><Button variant="outline" type="button">Cancel</Button></Link>
        </div>
      </form>
    </section>
  );
}
