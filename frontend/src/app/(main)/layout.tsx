import { TopNavBar } from "@/components/Navigation/TopNavBar";
import { SideNavBar } from "@/components/Navigation/SideNavBar";

export default function MainLayout({ children }: { children: React.ReactNode }) {
  return (
    <div className="flex pt-16 min-h-screen relative">
      <TopNavBar />
      <SideNavBar />
      <div className="flex-1 lg:ml-64 flex">
        {children}
      </div>
    </div>
  );
}
