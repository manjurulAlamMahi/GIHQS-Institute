import { useState, useEffect } from "react";
import { Button } from "@/components/ui/button";
import { Cookie, X } from "lucide-react";
import { initGoogleAnalytics } from "@/utils/analytics";

export function CookieConsent() {
  const [isVisible, setIsVisible] = useState(false);

  useEffect(() => {
    // Check if user has already accepted cookies
    const consent = localStorage.getItem("cookieConsent");
    if (consent === "true") {
      // If already accepted, initialize analytics
      initGoogleAnalytics();
    } else if (!consent) {
      // Small delay for better UX
      const timer = setTimeout(() => setIsVisible(true), 1000);
      return () => clearTimeout(timer);
    }
  }, []);

  const handleConsent = (accepted: boolean) => {
    localStorage.setItem("cookieConsent", accepted ? "true" : "false");
    setIsVisible(false);
    if (accepted) {
      initGoogleAnalytics();
    }
  };

  if (!isVisible) return null;

  return (
    <div className="fixed bottom-4 left-4 right-4 sm:left-6 sm:right-auto sm:w-100 z-50">
      <div className="bg-background/80 backdrop-blur-xl border border-border/50 shadow-2xl rounded-2xl overflow-hidden animate-in slide-in-from-bottom-8 fade-in-0 duration-700 ease-out">
        {/* Top Accent Line */}
        <div className="h-1 w-full bg-linear-to-r from-primary/40 via-primary to-primary/40" />
        
        <div className="p-6">
          <div className="flex items-start justify-between mb-4">
            <div className="flex items-center gap-3">
              <div className="flex items-center justify-center w-10 h-10 rounded-full bg-primary/10 text-primary shadow-inner">
                <Cookie className="h-5 w-5" />
              </div>
              <h3 className="font-semibold text-foreground text-lg">
                Cookie preferences
              </h3>
            </div>
            <button 
              onClick={() => handleConsent(false)}
              className="text-muted-foreground hover:text-foreground transition-colors p-1"
              aria-label="Close"
            >
              <X className="h-4 w-4" />
            </button>
          </div>
          
          <p className="text-muted-foreground text-sm leading-relaxed mb-6">
            We use cookies to improve your experience, personalize content, and analyze our traffic. 
            By continuing to use this site, you agree to our use of cookies.
          </p>
          
          <div className="flex items-center gap-3 w-full">
            <Button 
              variant="outline" 
              className="flex-1 text-xs font-medium hover:bg-secondary/50"
              onClick={() => handleConsent(false)}
            >
              Decline
            </Button>
            <Button 
              className="flex-1 text-xs font-medium shadow-md hover:shadow-lg transition-shadow"
              onClick={() => handleConsent(true)}
            >
              Accept All
            </Button>
          </div>
        </div>
      </div>
    </div>
  );
}
