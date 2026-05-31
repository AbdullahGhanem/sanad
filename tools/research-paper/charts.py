#!/usr/bin/env python3
"""
Regenerates the five Sanad figures as a unified, teal-branded infographic set
(four infographics + the system-architecture diagram).
Replaces the legacy MindBridge / CAMeLBERT artwork.
Outputs PNGs into ./figures/ next to this script.
"""
import os
import matplotlib
matplotlib.use("Agg")
import matplotlib.pyplot as plt
from matplotlib.patches import FancyBboxPatch, FancyArrowPatch, Circle, Wedge

OUT = os.path.join(os.path.dirname(os.path.abspath(__file__)), "figures")
os.makedirs(OUT, exist_ok=True)

# --- Sanad palette -----------------------------------------------------------
TEAL_700 = "#0F766E"
TEAL_600 = "#0D9488"
TEAL_500 = "#14B8A6"
TEAL_300 = "#5EEAD4"
TEAL_100 = "#CCFBF1"
TEAL_050 = "#F0FDFA"
TEAL_950 = "#042F2E"
SLATE_700 = "#334155"
SLATE_400 = "#94A3B8"
ROSE_600 = "#E11D48"
AMBER_600 = "#D97706"
WHITE = "#FFFFFF"

plt.rcParams["font.family"] = "DejaVu Sans"


def _chip(ax, x, y, w, h, fc, ec=None, rad=0.06, lw=1.4):
    p = FancyBboxPatch((x, y), w, h, boxstyle=f"round,pad=0.0,rounding_size={rad}",
                       fc=fc, ec=ec or fc, lw=lw, zorder=2)
    ax.add_patch(p)
    return p


def _band(ax, title, n):
    """Top teal title band shared by every figure."""
    ax.add_patch(plt.Rectangle((0, 1 - 0.20), 1, 0.20, fc=TEAL_700, ec="none", zorder=3,
                               transform=ax.transAxes))
    ax.text(0.020, 1 - 0.10, f"{n}", transform=ax.transAxes, ha="left", va="center",
            fontsize=12.5, fontweight="bold", color=TEAL_300, zorder=4)
    ax.text(0.058, 1 - 0.10, title, transform=ax.transAxes, ha="left", va="center",
            fontsize=11, fontweight="bold", color=WHITE, zorder=4)


def _frame(w, h):
    fig, ax = plt.subplots(figsize=(w, h), dpi=220)
    ax.set_xlim(0, 1); ax.set_ylim(0, 1); ax.axis("off")
    fig.patch.set_facecolor(WHITE)
    ax.add_patch(plt.Rectangle((0, 0), 1, 1, fc=TEAL_050, ec=TEAL_100, lw=2,
                               transform=ax.transAxes, zorder=0))
    return fig, ax


def save(fig, name):
    fig.savefig(f"{OUT}/{name}", bbox_inches="tight", pad_inches=0.05, facecolor=WHITE)
    plt.close(fig)


# ============================================================ Figure 1: crisis
def fig1():
    fig, ax = _frame(7.0, 2.1)
    _band(ax, "The Mental Health Crisis in Egyptian Universities", "1")

    # bars
    base_y = 0.16
    max_h = 0.50
    bx1, bx2 = 0.10, 0.30
    bw = 0.11
    h1 = max_h                      # 68.1%
    h2 = max_h * (29 / 68.1)        # ~29%
    ax.add_patch(plt.Rectangle((bx1, base_y), bw, h1, fc=TEAL_600, ec="none", zorder=3))
    ax.add_patch(plt.Rectangle((bx2, base_y), bw, h2, fc=TEAL_300, ec=TEAL_500, zorder=3))
    ax.text(bx1 + bw / 2, base_y + h1 + 0.04, "68.1%", ha="center", fontsize=15,
            fontweight="bold", color=TEAL_700)
    ax.text(bx2 + bw / 2, base_y + h2 + 0.04, "28–30%", ha="center", fontsize=12,
            fontweight="bold", color=SLATE_700)
    ax.text(bx1 + bw / 2, base_y - 0.05, "Egyptian\nstudents", ha="center", va="top",
            fontsize=8.5, color=SLATE_700)
    ax.text(bx2 + bw / 2, base_y - 0.05, "Global\naverage", ha="center", va="top",
            fontsize=8.5, color=SLATE_700)
    # baseline
    ax.plot([0.07, 0.45], [base_y, base_y], color=SLATE_400, lw=1.2, zorder=2)

    # right info card
    _chip(ax, 0.52, 0.16, 0.43, 0.56, WHITE, ec=TEAL_100, rad=0.05)
    ax.text(0.535, 0.63, "Baklola et al. (2023)", fontsize=11, fontweight="bold",
            color=TEAL_700)
    for i, t in enumerate([
        "3,240 students surveyed",
        "21 Egyptian universities",
        "68.1% psychological distress",
        "More than twice the global rate",
    ]):
        ax.text(0.55, 0.55 - i * 0.085, "•", fontsize=12, color=TEAL_500, va="center")
        ax.text(0.575, 0.55 - i * 0.085, t, fontsize=9.3, color=SLATE_700, va="center")
    save(fig, "fig1_crisis.png")


# ====================================================== Figure 2: helpseeking gap
def fig2():
    fig, ax = _frame(7.0, 2.45)
    _band(ax, "Why Aren’t Students Getting Help?", "2")

    # donut for help-seeking gap
    cx, cy, r = 0.165, 0.47, 0.145
    ax.add_patch(Wedge((cx, cy), r, 90, 90 - 360 * 0.903, fc=TEAL_600, zorder=3))
    ax.add_patch(Wedge((cx, cy), r, 90 - 360 * 0.903, 90, fc=TEAL_300, zorder=3))
    ax.add_patch(Circle((cx, cy), r * 0.56, fc=TEAL_050, ec=TEAL_100, lw=1.5, zorder=4))
    ax.text(cx, cy + 0.025, "90.3%", ha="center", va="center", fontsize=13,
            fontweight="bold", color=TEAL_700, zorder=5)
    ax.text(cx, cy - 0.045, "never sought\nhelp", ha="center", va="center", fontsize=7,
            color=SLATE_700, zorder=5)
    _chip(ax, 0.025, 0.20, 0.28, 0.085, "#FFE4E6", ec=ROSE_600, rad=0.03, lw=1.1)
    ax.text(0.165, 0.2425, "Only 9.7% access care", ha="center", va="center",
            fontsize=8.5, fontweight="bold", color=ROSE_600, zorder=3)

    # barrier columns (narrow, non-overlapping)
    def col(x, w, header, hc, items):
        _chip(ax, x, 0.555, w, 0.10, hc, rad=0.035)
        ax.text(x + w / 2, 0.605, header, ha="center", va="center", fontsize=8.3,
                fontweight="bold", color=WHITE, zorder=3)
        for i, t in enumerate(items):
            yy = 0.46 - i * 0.085
            ax.text(x + 0.005, yy, "›", fontsize=9, color=hc, va="center", fontweight="bold")
            ax.text(x + 0.035, yy, t, fontsize=7.6, color=SLATE_700, va="center")

    col(0.37, 0.29, "STRUCTURAL BARRIERS", TEAL_600, [
        "18 hospitals for 100M+ people",
        "No services for mild distress",
        "Counselling barely exists",
    ])
    col(0.685, 0.29, "INDIVIDUAL BARRIERS", AMBER_600, [
        "Stigma remains heavy",
        "Asking feels impossible",
        "No Arabic-language tools",
    ])
    ax.text(0.5, 0.075, "Nine out of ten distressed students go entirely unsupported.",
            ha="center", va="center", fontsize=9, fontweight="bold", color=TEAL_950)
    save(fig, "fig2_gap.png")


# ================================================= Figure 3: Sanad removes barriers
def fig3():
    fig, ax = _frame(7.0, 2.1)
    _band(ax, "Sanad — Removing the Barriers", "3")

    pairs = [
        ("Stigma", "Anonymous & free\nNo account required"),
        ("Inaccessibility", "Web-based\nUse on any phone"),
        ("No Arabic tools", "Native Arabic (RTL)\n+ supportive AI chat"),
    ]
    n = len(pairs)
    colw = 0.30
    gap = (1 - n * colw) / (n + 1)
    for i, (barrier, solution) in enumerate(pairs):
        x = gap + i * (colw + gap)
        # barrier chip
        _chip(ax, x, 0.42, colw, 0.17, "#FEE2E2", ec=ROSE_600, rad=0.04, lw=1.2)
        ax.text(x + colw / 2, 0.55, "BARRIER", ha="center", fontsize=7,
                fontweight="bold", color=ROSE_600)
        ax.text(x + colw / 2, 0.47, barrier, ha="center", fontsize=10,
                fontweight="bold", color=SLATE_700)
        # arrow down
        ax.add_patch(FancyArrowPatch((x + colw / 2, 0.41), (x + colw / 2, 0.345),
                     arrowstyle="-|>", mutation_scale=14, color=TEAL_600, lw=2, zorder=4))
        # solution chip
        _chip(ax, x, 0.14, colw, 0.19, TEAL_600, rad=0.04)
        ax.text(x + colw / 2, 0.295, "SANAD", ha="center", fontsize=7.5,
                fontweight="bold", color=TEAL_100)
        ax.text(x + colw / 2, 0.21, solution, ha="center", va="center", fontsize=8.8,
                color=WHITE, linespacing=1.3)
    save(fig, "fig3_barriers.png")


# ====================================================== Figure 4: how Sanad works
def fig4():
    fig, ax = _frame(7.0, 1.65)
    _band(ax, "How Sanad Works — The Technology Stack", "4")

    steps = [
        ("Bilingual UI", "Laravel 12 ·\nLivewire 4 (RTL)"),
        ("Screening", "PHQ-9 & GAD-7\nArabic-validated"),
        ("LLM analysis", "laravel/ai →\nGPT · Claude · Gemini"),
        ("Ensemble", "70/30 fusion +\nSanad chatbot"),
    ]
    n = len(steps)
    colw = 0.205
    gap = (1 - n * colw) / (n + 1)
    for i, (head, body) in enumerate(steps):
        x = gap + i * (colw + gap)
        _chip(ax, x, 0.16, colw, 0.50, WHITE, ec=TEAL_500, rad=0.05, lw=1.6)
        ax.add_patch(plt.Rectangle((x, 0.50), colw, 0.16, fc=TEAL_600, ec="none", zorder=3))
        ax.text(x + colw / 2, 0.58, head, ha="center", va="center", fontsize=8.5,
                fontweight="bold", color=WHITE, zorder=4)
        ax.text(x + colw / 2, 0.33, body, ha="center", va="center", fontsize=8.3,
                color=SLATE_700, linespacing=1.35, zorder=4)
        if i < n - 1:
            ax.add_patch(FancyArrowPatch((x + colw + 0.01, 0.41),
                         (x + colw + gap - 0.01, 0.41), arrowstyle="-|>",
                         mutation_scale=13, color=TEAL_700, lw=2, zorder=5))
    save(fig, "fig4_tech.png")


# ================================================ Figure 5: architecture diagram
def fig5():
    fig, ax = plt.subplots(figsize=(7.2, 5.4), dpi=220)
    ax.set_xlim(0, 1); ax.set_ylim(0, 1); ax.axis("off")
    fig.patch.set_facecolor(WHITE)
    ax.add_patch(plt.Rectangle((0, 0), 1, 1, fc=WHITE, ec=TEAL_100, lw=2, zorder=0))
    # title band
    ax.add_patch(plt.Rectangle((0, 0.945), 1, 0.055, fc=TEAL_700, ec="none", zorder=3))
    ax.text(0.02, 0.9725, "5", ha="left", va="center", fontsize=12.5,
            fontweight="bold", color=TEAL_300, zorder=4)
    ax.text(0.055, 0.9725, "Sanad System Architecture", ha="left", va="center",
            fontsize=11, fontweight="bold", color=WHITE, zorder=4)

    def lane(x, y, w, h, fc, ec, lw=1.3):
        ax.add_patch(FancyBboxPatch((x, y), w, h,
                     boxstyle="round,pad=0.0,rounding_size=0.012",
                     fc=fc, ec=ec, lw=lw, zorder=2))

    def label(x, y, text, fs=7.6, color=SLATE_700, bold=False, ha="center", z=5):
        ax.text(x, y, text, ha=ha, va="center", fontsize=fs,
                fontweight="bold" if bold else "normal", color=color, zorder=z)

    def box(cx, cy, w, h, title, fc=WHITE, ec=TEAL_500, tc=TEAL_700, sub=None, lw=1.3):
        lane(cx - w / 2, cy - h / 2, w, h, fc, ec, lw)
        if sub:
            label(cx, cy + h * 0.18, title, fs=7.8, color=tc, bold=True)
            label(cx, cy - h * 0.22, sub, fs=6.6, color=SLATE_700)
        else:
            label(cx, cy, title, fs=7.8, color=tc, bold=True)

    def arrow(x1, y1, x2, y2, color=TEAL_700, lw=1.8, style="-|>"):
        ax.add_patch(FancyArrowPatch((x1, y1), (x2, y2), arrowstyle=style,
                     mutation_scale=11, color=color, lw=lw, zorder=6))

    # ---- Layer 0: student entry
    box(0.5, 0.90, 0.64, 0.045, "Student  ·  anonymous, no account required",
        fc=TEAL_600, ec=TEAL_700, tc=WHITE)

    # ---- Layer 1: presentation (Livewire)
    lane(0.05, 0.755, 0.90, 0.105, TEAL_050, TEAL_300)
    label(0.075, 0.835, "Presentation — Livewire 4 + Tailwind (Arabic RTL / English LTR)",
          fs=7.4, color=TEAL_700, bold=True, ha="left")
    for i, (t) in enumerate(["Screening\nWizard", "Supportive\nChat", "Results +\nPDF export"]):
        bx = 0.13 + i * 0.30
        box(bx, 0.785, 0.22, 0.045, t, fc=WHITE, ec=TEAL_500, tc=TEAL_700)

    # ---- Layer 2: application core
    box(0.5, 0.685, 0.90, 0.05,
        "Laravel 12 application core", fc=TEAL_100, ec=TEAL_500, tc=TEAL_950,
        sub="Guest-session middleware  ·  per-answer autosave  ·  queueable AI calls")

    # ---- Layer 3: two columns — scoring (left) and AI (right)
    lane(0.05, 0.435, 0.43, 0.19, "#ECFEFF", TEAL_500)
    label(0.265, 0.602, "Deterministic Scoring (PHP)", fs=7.6, color=TEAL_700, bold=True)
    for i, t in enumerate(["PHQ-9 service", "GAD-7 service", "Combined (max)",
                            "Ensemble 70/30", "Context injection"]):
        rx = 0.10 + (i % 2) * 0.205
        ry = 0.555 - (i // 2) * 0.052
        box(rx + 0.085, ry, 0.165, 0.04, t, fc=WHITE, ec=TEAL_300, tc=SLATE_700)

    lane(0.52, 0.435, 0.43, 0.19, "#FFFBEB", AMBER_600)
    label(0.735, 0.602, "AI Layer — laravel/ai", fs=7.6, color=AMBER_600, bold=True)
    box(0.63, 0.553, 0.18, 0.04, "DistressAnalyzer", fc=WHITE, ec=TEAL_300, tc=SLATE_700)
    box(0.84, 0.553, 0.18, 0.04, "SanadChat", fc=WHITE, ec=TEAL_300, tc=SLATE_700)
    box(0.735, 0.495, 0.40, 0.04, "Provider router (configurable)",
        fc=TEAL_600, ec=TEAL_700, tc=WHITE)
    for i, t in enumerate(["OpenAI", "Anthropic", "Gemini"]):
        box(0.605 + i * 0.13, 0.452, 0.115, 0.034, t, fc=WHITE, ec=AMBER_600, tc=SLATE_700)

    # ---- Layer 4: crisis pipeline (cross-cutting, rose)
    lane(0.05, 0.345, 0.90, 0.05, "#FFE4E6", ROSE_600)
    label(0.5, 0.385, "Crisis pipeline (always-on)", fs=7.4, color=ROSE_600, bold=True)
    label(0.5, 0.360,
          "PHQ-9 item 9 ≥ 2  +  keyword match   →   CrisisEvent log   →   CrisisHelpResource (hotline · Nefsy.com)",
          fs=6.6, color=SLATE_700)

    # ---- Layer 5: persistence
    lane(0.05, 0.165, 0.90, 0.13, TEAL_050, TEAL_300)
    label(0.075, 0.275, "Persistence — Eloquent ORM + relational database",
          fs=7.4, color=TEAL_700, bold=True, ha="left")
    ents = ["Questionnaire /\nQuestion / Option", "ScreeningSession /\nSessionAnswer",
            "ChatMessage", "Recommendation", "CrisisEvent", "AiProviderSetting"]
    for i, t in enumerate(ents):
        bx = 0.135 + (i % 3) * 0.275
        by = 0.225 - (i // 3) * 0.045
        box(bx, by, 0.24, 0.038, t, fc=WHITE, ec=TEAL_300, tc=SLATE_700)

    # ---- Layer 6: admin analytics
    box(0.5, 0.095, 0.90, 0.045, "Filament 5 admin & analytics", fc=TEAL_600,
        ec=TEAL_700, tc=WHITE,
        sub=None)
    label(0.5, 0.045,
          "aggregate & anonymous: screening stats · severity & trend charts · crisis stats · CSV export",
          fs=6.6, color=SLATE_700)

    # ---- flow arrows (centre spine)
    arrow(0.5, 0.877, 0.5, 0.862)        # student -> presentation
    arrow(0.5, 0.752, 0.5, 0.712)        # presentation -> core
    arrow(0.36, 0.659, 0.265, 0.628)     # core -> scoring
    arrow(0.64, 0.659, 0.735, 0.628)     # core -> AI
    arrow(0.735, 0.532, 0.735, 0.516)    # agents -> router
    arrow(0.265, 0.432, 0.265, 0.398, color=ROSE_600)   # scoring -> crisis
    arrow(0.5, 0.342, 0.5, 0.298)        # crisis -> persistence
    arrow(0.5, 0.162, 0.5, 0.119)        # persistence -> admin
    # AI results flow back to scoring (ensemble) — dashed
    arrow(0.52, 0.553, 0.475, 0.553, color=AMBER_600, lw=1.4, style="-|>")

    fig.savefig(f"{OUT}/fig5_architecture.png", bbox_inches="tight", pad_inches=0.05,
                facecolor=WHITE)
    plt.close(fig)


if __name__ == "__main__":
    fig1(); fig2(); fig3(); fig4(); fig5()
    print("Figures written to", OUT)
    for f in sorted(os.listdir(OUT)):
        print(" -", f)
