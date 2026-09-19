# Claude Code Skills Library

This directory contains reusable Claude Code skills for AI-assisted software development.

## Skill Categories

### 🎯 Planning & Analysis
- **autoplan** — Automatic planning with structured breakdown
- **spec** — Specification writing and requirements gathering
- **investigate** — Code investigation and analysis
- **learn** — Learning and documentation generation

### 🎨 Design & UX
- **design-review** — Design system and UI/UX review
- **design-consultation** — Design consultation and feedback
- **design-shotgun** — Rapid design exploration
- **design-html** — HTML design implementation
- **diagram** — Diagram and flowchart generation

### 👁️ Code Review & Quality
- **review** — Comprehensive code review
- **careful** — Careful bug hunting and edge case analysis
- **qa** — QA testing and validation
- **qa-only** — QA-focused validation
- **guard** — Security and safety auditing
- **health** — Code health and metrics analysis

### 📱 Platform-Specific
- **ios-clean** — iOS code cleanup and refactoring
- **ios-design-review** — iOS design review
- **ios-fix** — iOS bug fixing
- **ios-qa** — iOS testing and QA
- **ios-sync** — iOS synchronization tools

### 🚀 Deployment & Release
- **ship** — Shipping and release management
- **land-and-deploy** — Landing branches and deployment
- **setup-deploy** — Deployment setup and configuration
- **document-release** — Release documentation generation
- **freeze** — Code freeze management
- **unfreeze** — Code unfreeze management

### 🔄 Context & State Management
- **context-save** — Save context and session state
- **context-restore** — Restore saved context
- **setup-browser-cookies** — Browser cookie setup
- **setup-gbrain** — GBrain setup and configuration
- **sync-gbrain** — Synchronize with GBrain

### 📊 Planning & Review Meetings
- **plan-eng-review** — Engineering review planning
- **plan-design-review** — Design review planning
- **plan-devex-review** — Developer experience review
- **plan-ceo-review** — CEO/stakeholder review planning
- **plan-tune** — Plan tuning and refinement
- **office-hours** — Office hours and discussions
- **retro** — Retrospective meetings

### 🔍 Investigation & Reporting
- **landing-report** — Landing page analysis and reporting
- **scrape** — Web scraping and data collection
- **benchmark** — Performance benchmarking
- **benchmark-models** — Model benchmarking
- **canary** — Canary deployment testing
- **health** — System health monitoring

### 🛠️ Tools & Utilities
- **browse** — Browser automation and control
- **codex** — Code analysis and indexing
- **cso** — Custom search and optimization
- **skillify** — Skill creation and management
- **gstack-upgrade** — Gstack upgrade procedures
- **make-pdf** — PDF generation
- **open-gstack-browser** — Open gstack browser
- **pair-agent** — Pair programming assistant
- **hackernews-frontpage** — Hacker News integration
- **gstack_main** — Main gstack skill and overview

## Skills from gstack

These skills are imported from [gstack](https://github.com/garrytan/gstack) by Garry Tan (YC CEO). 

**gstack** is an open-source AI engineering toolkit that enables individuals to ship at the scale of large teams. It provides:

- 23+ specialized agent patterns
- 8 power tools for software development
- Complete CI/CD and deployment automation
- Design review and UX audit systems
- Security and compliance checking
- Comprehensive QA and testing frameworks

### Key Features
- **No boilerplate** — Pure Markdown configuration
- **Modular design** — Mix and match skills
- **MIT License** — Free to use and modify
- **Production-tested** — Used by Garry Tan at Y Combinator

## Using These Skills

Each skill is defined in a `{skillname}_SKILL.md` file. These can be:

1. **Listed in your Claude Code configuration** (.claude/config.json)
2. **Invoked with slash commands** (e.g., `/review`, `/design-review`)
3. **Chained together** for complex workflows
4. **Customized** for your specific needs

## Contributing

To add or modify skills:

1. Edit or add skill definition files in this directory
2. Follow the `SKILL.md` format from gstack
3. Commit and push your changes
4. Reference in `.claude/config.json` if needed

## Resources

- [gstack Repository](https://github.com/garrytan/gstack)
- [gstack README](https://github.com/garrytan/gstack/blob/main/README.md)
- [gstack Architecture](https://github.com/garrytan/gstack/blob/main/ARCHITECTURE.md)
- [gstack Contributing Guide](https://github.com/garrytan/gstack/blob/main/CONTRIBUTING.md)

---

**Total Skills**: 55  
**Last Updated**: 2026-09-19  
**Source**: https://github.com/garrytan/gstack
