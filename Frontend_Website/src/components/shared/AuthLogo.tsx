import Logo from "@/assets/icons/MainLogo.svg?react";
import { Link } from "react-router";

export function AuthLogo() {
  return (
    <div className="flex flex-col items-center justify-center space-y-6 mb-8 mt-4">
      <Link to="/" className="inline-block transition-transform hover:scale-[1.02]">
        <Logo className="w-38 h-auto text-[#0E4A3B] sm:w-42" />
      </Link>
      <div className="max-w-100 text-center px-4">
        <p className="text-[#3A564A] text-[13px] md:text-sm font-medium leading-relaxed">
          Advancing global leadership in healthcare quality patient safety: and responsible innovation.
        </p>
      </div>
    </div>
  );
}