import fs from 'fs';
import path from 'path';

const mapping = {
  // About API
  useGetAboutInstituteQuery: '@/features/about/api/aboutApi',
  useGetAboutContactQuery: '@/features/about/api/aboutApi',
  useGetVisionMissionValuesQuery: '@/features/about/api/aboutApi',
  useGetPoliciesGovernanceQuery: '@/features/about/api/aboutApi',
  useGetStrategicAdvisoryQuery: '@/features/about/api/aboutApi',
  useGetAccreditationReviewQuery: '@/features/about/api/aboutApi',
  useSubmitContactMessageMutation: '@/features/about/api/aboutApi',
  // Home API
  useGetHomeServicesPathwaysQuery: '@/features/home/api/homeApi',
  useGetHomeFlagshipCertificationsQuery: '@/features/home/api/homeApi',
  // Advisory API
  useGetAdvisoryServicesQuery: '@/features/advisory/api/advisoryApi',
  useGetRequestAdvisoryConsultationQuery: '@/features/advisory/api/advisoryApi',
  useSubmitAdvisoryRequestMutation: '@/features/advisory/api/advisoryApi',
  // Accreditation API
  useGetAccreditationHeaderQuery: '@/features/accreditation/api/accreditationApi',
  useGetAccreditationDetailsQuery: '@/features/accreditation/api/accreditationApi',
  useGetAccreditationFeesQuery: '@/features/accreditation/api/accreditationApi',
  useGetAccreditationApplyHeroQuery: '@/features/accreditation/api/accreditationApi'
};

const srcPath = path.join(process.cwd(), 'src');

function getFiles(dir, files = []) {
  const fileList = fs.readdirSync(dir);
  for (const file of fileList) {
    const name = path.join(dir, file);
    if (fs.statSync(name).isDirectory()) {
      getFiles(name, files);
    } else if (name.endsWith('.ts') || name.endsWith('.tsx')) {
      files.push(name);
    }
  }
  return files;
}

const files = getFiles(srcPath);

files.forEach(file => {
  let content = fs.readFileSync(file, 'utf-8');
  
  const importRegex = /import\s*\{([^}]+)\}\s*from\s*['"]@\/features\/public\/api\/publicApi['"]/g;
  
  if (importRegex.test(content)) {
    console.log(`Updating imports in ${file}`);
    content = content.replace(importRegex, (match, p1) => {
      const hooks = p1.split(',').map(s => s.trim()).filter(Boolean);
      
      const newImports = {};
      hooks.forEach(hook => {
        const targetPath = mapping[hook];
        if (!targetPath) {
          console.warn(`WARNING: Unknown hook ${hook} found in ${file}`);
          return;
        }
        if (!newImports[targetPath]) newImports[targetPath] = [];
        newImports[targetPath].push(hook);
      });
      
      let replacementString = '';
      for (const [targetPath, hookArray] of Object.entries(newImports)) {
        replacementString += `import { ${hookArray.join(', ')} } from "${targetPath}";\n`;
      }
      return replacementString.trim();
    });
    
    fs.writeFileSync(file, content, 'utf-8');
  }
});

console.log("Refactoring complete.");
