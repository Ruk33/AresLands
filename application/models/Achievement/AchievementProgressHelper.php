<?php namespace Models\Achievement;

class AchievementProgressHelper
{
    /**
     * @var AchievementRepository
     */
    protected $achievement;

    /**
     * @var AchievementCharacterProgressRepository
     */
    protected $progress;

    /**
     * @return integer
     */
    public function getCurrentProgress()
    {
        if ($this->achievement->mustBuyVipObject()) {
            return (int) $this->progress->hasCompletedVipObject();
        }

        if ($this->achievement->mustCompleteQuest()) {
            if ($this->achievement->getQuestAmountToComplete() > 0) {
                return $this->progress->getCharacter()->getCompletedQuestsAmount();
            } else {
                return (int) $this->progress->hasCompletedQuest();
            }
        }

        if ($this->achievement->mustExplore()) {
            return 0;
        }

        if ($this->achievement->mustGetOrb()) {
            return (int) $this->progress->hasCompletedOrb();
        }

        if ($this->achievement->mustKillMonster()) {
            return (int) $this->progress->hasCompletedKillNpc();
        }

        if ($this->achievement->mustLevelUp()) {
            return (int) $this->progress->getCharacter()->level;
        }

        if ($this->achievement->mustReachRank()) {
            return 0;
        }

        if ($this->achievement->mustTravel()) {
            return (int) $this->progress->hasCompletedTravel();
        }

        if ($this->achievement->mustWinPve()) {
            return $this->progress->getCharacter()->getPves();
        }

        if ($this->achievement->mustWinPvp()) {
            return $this->progress->getCharacter()->getPvps();
        }

        return 0;
    }

    public function getFinalProgress()
    {
        if ($this->achievement->mustExplore()) {
            return $this->achievement->getExploringTimeRequired();
        }

        if ($this->achievement->mustLevelUp()) {
            return $this->achievement->getLevelToBeReached();
        }

        if ($this->achievement->mustReachRank()) {
            return $this->achievement->getRankToBeReached();
        }

        if ($this->achievement->mustWinPve()) {
            return $this->achievement->getPveWinNumberRequired();
        }

        if ($this->achievement->mustWinPvp()) {
            return $this->achievement->getPvpWinNumberRequired();
        }

        if ($this->achievement->getQuestAmountToComplete() > 0) {
            return $this->achievement->getQuestAmountToComplete();
        }

        return 1;
    }

    public function getPercentage()
    {
        return $this->getCurrentProgress() * 100 / $this->getFinalProgress();
    }

    public function __construct(AchievementRepository $achievement,
                                AchievementCharacterProgressRepository $progress)
    {
        $this->achievement = $achievement;
        $this->progress = $progress;
    }
} 